<? namespace Selbil\MailQueue;

class Helper{

	public $defaultConfig = [
		"host"		=> "localhost",
		"port"		=> 3306,
		"dbname"	=> "your-db-name",
		"charset"	=> "UTF8",
		"username"	=> "root",
		"password"	=> "root",
		"table"		=> "mail_queue",
		"sender"	=> NULL,
	];

	public function getObjectOf($array){
		return json_decode(json_encode($array , false));
	}

}