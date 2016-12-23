# SelBil Mail Queue

This mail queue created for organising your mail queue and making your server more easer. By using **Mail Queue** class you can :
* Add sender and receiver your queue
* In your cron jobs files or another set mailer and run queue
* You can watch your mail status from your database and resend your mail to your customer or someone else

## Installation
### Cloning
You can download as a **.zip** file by clicking above or clone this repository :
```
	git clone https://github.com/selbil/mail-queue
```

### With Composer
You can use this class from __composer__
```
	composer require 'selbil/mail-queue' : 'dev-master'
```

##Usage
__main.php__
```php
	// Use it because of truer sending and log
	date_default_timezone_set("Europe/Istanbul");

	// Require or include your composer autoloader
	require_once "vendor/autoload.php";

	$queueConfig = [
		"dbname"	=> "my_queue_test_db",
		"sender"	=> "emredoganm@live.com",
	];

	// SelBil Mail Queue uses this default config, you can customize it with setConfig() function
	/*
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
	*/

	$queue = new Selbil\MailQueue\Queue;

	// Set your database config and create schema for mail queue
	// SelBil Mail Queue uses MySQL
	$queue->setConfig($queueConfig)
		->createSchema();
```

__cron-for-queue.php__
```php
	date_default_timezone_set("Europe/Moscow");
	
	require 'vendor/autoload.php';
	
	// We are re-initilizating our database config file for reading
	$config = [
		"dbname"	=> "kasirga",
	];
	
	// These are for your smtp mail server config
	// I am using my Gmail account for testing
	$mailConfig = [
		"host" 		=> "smtp.gmail.com",
		"username"	=> "emredoganm06@gmail.com",
		"password"	=> "my-beautiful-password"
	];

	
	$mailer = new Selbil\MailQueue\Mail;
	
	// Database config registration
	$mailer->setDatabaseConfig($config);

	// Mailer config registration
	$mailer->setConfig($mailConfig);
	
	// Number of mail for one cycle
	$mailer->setLimit(5);

	// Run the query for your customers or users
	$mailer->run();
```