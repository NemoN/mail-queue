<? namespace Selbil\MailQueue;

class Helper{

    public $defaultConfig = [
        "host"      => "localhost",
        "port"      => 3306,
        "dbname"    => "your-db-name",
        "charset"   => "UTF8",
        "username"  => "root",
        "password"  => "root",
        "table"     => "mail_queue",
        "sender"    => NULL,
    ];

    public $mailConfig = [
        "use_smtp"          => true,
        "host"              => NULL,
        "smtp_auth"         => true,
        "username"          => 'user@example.com',
        "password"          => "secret",
        "smtp_secure"       => "tls",
        "port"              => 587,
        "debug"             => true,
    ];

    public function randomDate($start_date, $end_date){
        $min = strtotime($start_date);
        $max = strtotime($end_date);
        $val = rand($min, $max);
        return date('Y-m-d H:i:s', $val);
    }

    public function getObjectOf($array){
        return json_decode(json_encode($array , false));
    }

}