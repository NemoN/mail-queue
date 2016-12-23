<? namespace Selbil\MailQueue;

use PDO;
use PDOException;

class DatabaseBundle extends PDO{

    public $conn,$config;

    private $dbOptions = [
        PDO::ATTR_PERSISTENT            => true,
        PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_OBJ
    ];

    public function __construct($config){
        $this->config = $config;
        $this->setDatabase();
    }

    protected function setDatabase(){
        $connectionString = "mysql:host=".$this->config->host.";";
        $connectionString .= "port=".$this->config->port.";";
        $connectionString .= "dbname=".$this->config->dbname.";";
        $connectionString .= "charset=".$this->config->charset.";";

        $this->conn = new PDO($connectionString , $this->config->username , $this->config->password , $this->dbOptions);
        $this->conn->query("SET NAMES utf8;");
        return $this;
    }

    public function insertArray($array = [] , $table = NULL){
        if($array != [] && $table){
            $sql = "INSERT INTO ".$table."(";
            $sql .= implode("," , array_keys($array));
            $sql .= ") VALUES (";
            $values = array_map(function($item){
                return $item == NULL ? 'NULL' : "'".addslashes($item)."'";
            },$array);
            $sql .= implode("," , $values).")";
            $this->tryQuery($sql);
            return true;
        }
        return false;
    }

    public function tryQuery($sql){
        try{
            $statement = $this->conn->prepare($sql);
            $statement->execute();
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }

}