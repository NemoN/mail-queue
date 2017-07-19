<?php namespace Selbil\MailQueue;

class Schema extends DatabaseBundle{

    public function create(){
        $sql = "CREATE TABLE IF NOT EXISTS `".$this->config->table."` (";
        $sql .= "id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,sender VARCHAR(255) NOT NULL,senderName VARCHAR(255),receiver VARCHAR(255) NOT NULL,receiverName VARCHAR(255),reply VARCHAR(255),replyName VARCHAR(255),bcc VARCHAR(255),subject VARCHAR(255) NOT NULL,content TEXT NOT NULL,is_sent TINYINT DEFAULT '0',has_error TINYINT DEFAULT '0',error_message VARCHAR(255),try_again TINYINT DEFAULT '0',send_after DATETIME,sent_at DATETIME,created_at DATETIME NOT NULL,updated_at DATETIME NOT NULL);";
        $this->conn->exec($sql);
    }

}
