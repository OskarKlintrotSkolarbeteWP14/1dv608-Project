<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-07
 * Time: 12:52
 */


class DatabaseConnection
{
    private $connection;
    private $statement;

    public function __construct() {
        try {
            $settings = parse_ini_file('Database.Settings');
            $this->connection = new PDO('mysql:host='.$settings['DB_HOST'].';dbname='.$settings['DB_DATABASE'],$settings['DB_USERNAME'],$settings['DB_PASSWORD'],
                array(PDO::ATTR_EMULATE_PREPARES => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch(PDOException $e) {
//            echo 'Error: ' . $e->getMessage();
        }
    }

    public function prepare($query){
        $this->statement = $this->connection->prepare($query);
    }
    public function bindValue($param, $value){
        $this->statement->bindValue($param, $value);
    }
    public function execute(){
        return $this->statement->execute();
    }
    public function fetch()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchAll(){
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function rowCount(){
        return $this->statement->rowCount();
    }
}