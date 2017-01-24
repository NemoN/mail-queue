<?php namespace Selbil\MailQueue;

class Queue{

    public $config,$helper;
    protected $db,$bundle;

    public function __construct(){
        $this->helper = new Helper;
    }

    public function setConfig($attributes = []){
        $this->config = $this->helper->getObjectOf(array_merge($this->helper->defaultConfig , $attributes));
        $this->bundle = new DatabaseBundle($this->config);
        $this->db = $this->bundle->conn;
        return $this;
    }

    public function setDefaultSender($sender){
        $this->config->sender = trim($sender);
        return $this;
    }

    public function add($attributes = []){
        $defaults = [
            "created_at"        => date("Y-m-d H:i:s"), 
            "updated_at"        => date("Y-m-d H:i:s"),
            "sender"            => $this->config->sender,
        ];
        $attributes = array_merge($defaults , $attributes);
        if($attributes["sender"] === NULL || trim($attributes["sender"]) === ""){
            die("An queued email needs a sender!");
        }
        if(count($attributes) > 3) return $this->bundle->insertArray($attributes , $this->config->table);
        else die("Cannot add to queue. Please confirm that you fill all required column data!");
    }

    public function createSchema(){
        $schema = new Schema($this->config);
        return $schema->create();
    }

}