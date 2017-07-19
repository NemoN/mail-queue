<?php namespace Selbil\MailQueue;

use PHPMailer;

class Mail{

    public $limit , $mailer;
    protected $config , $dbConfig;
    private $nextQueue;

    protected $queueSQL = "SELECT * FROM `::table-name::` WHERE (is_sent = 0 OR (is_sent = 1 AND try_again = 1 AND has_error = 1)) AND (send_after >= '::now::' || send_after IS NULL) ORDER BY created_at,updated_at LIMIT ::limit::";

    public function __construct(){
        $this->helper = new Helper;
        $this->limit = 30;
    }

    public function setDatabaseConfig($attributes = []){
        $this->dbConfig = $this->helper->getObjectOf(array_merge($this->helper->defaultConfig , $attributes));
        $this->bundle = new DatabaseBundle($this->dbConfig);
        $this->db = $this->bundle->conn;
        return $this;
    }

    public function setLimit($limit){
        $this->limit = (intval($limit) == 0 || intval($limit) > 100) ? $this->limit : intval($limit);
        return $this;
    }

    public function setConfig($config = []){
        $this->config = $this->helper->getObjectOf(array_merge($this->helper->mailConfig , $config));
        return $this;
    }

    protected function createMailer(){
        $this->mailer = new PHPMailer;
        if($this->config->use_smtp) $this->mailer->isSMTP();
        $this->mailer->Host = $this->config->host;
        $this->mailer->SMTPAuth = $this->config->smtp_auth;
        $this->mailer->Username = $this->config->username;
        $this->mailer->Password = $this->config->password;
        $this->mailer->SMTPSecure = $this->config->smtp_secure;
        $this->mailer->Port = $this->config->port;
        return $this;
    }

    public function run(){
        $this->createMailer()
            ->getNextQueue()
            ->runQuery();
    }

    protected function runQuery(){
        foreach ($this->nextQueue as $mailOperation) {
            $this->sendMailFor($mailOperation);
        }
    }

    protected function sendMailFor($mailOperation){
        if($mailOperation->senderName) $this->mailer->setFrom($mailOperation->sender , $mailOperation->senderName);
        else $this->mailer->setFrom($mailOperation->sender);
        if ($mailOperation->receiverName) $this->mailer->addAddress($mailOperation->receiver, $mailOperation->receiverName);
        else $this->mailer->addAddress($mailOperation->receiver);
        if($mailOperation->reply && $mailOperation->replyName) $this->mailer->addReplyTo($mailOperation->reply, $mailOperation->replyName);
        elseif($mailOperation->reply) $this->mailer->addReplyTo($mailOperation->reply);
        if($mailOperation->bcc) $this->mailer->addBCC($mailOperation->bcc);
        $this->mailer->isHTML(true);

        $this->mailer->Subject = $mailOperation->subject;
        $this->mailer->Body    = $mailOperation->content;
        if($this->config->debug){
            try{
                $this->mailer->send();
            } catch (phpmailerException $e) {
                $errorMessage = $e->errorMessage();
                echo $errorMessage;
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                echo $errorMessage;
            }
        }else{
            if(!$this->mailer->send()){
                $errorMessage = $this->mailer->ErrorInfo;
            }
        }
        $this->updateMailOperationStatus($mailOperation->id, (isset($errorMessage) ? $errorMessage : NULL));
    }

    private function updateMailOperationStatus($id , $errorMessage = NULL){
        $sql = "UPDATE `".$this->dbConfig->table."` SET sent_at = '".date("Y-m-d H:i:s")."', is_sent = 1, has_error = ".($errorMessage ? "1" : "0").", error_message = ".($errorMessage ? "'".$errorMessage."'" : 'NULL')." WHERE id = ".$id;
        $this->bundle->tryQuery($sql);
    }

    private function getNextQueue(){
        $statement = $this->db->prepare($this->replaceSQL());
        $statement->execute();
        $this->nextQueue = $statement->fetchAll();
        return $this;
    }

    private function replaceSQL(){
        $replacer = [
            "::now::"           => date("Y-m-d H:i:s"),
            "::table-name::"    => $this->dbConfig->table,
            "::limit::"         => $this->limit,
        ];
        return strtr($this->queueSQL , $replacer);
    }

}
