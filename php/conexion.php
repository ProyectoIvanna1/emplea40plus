<?php

class Conexion {
    private $_conn = NULL;
    public function __construct() {
    
    }

    public function conectar() {
        try {
            $host = "localhost";
            $dbname = "proyecto";
            $user = "root";
            $pass = ""; //hostingionos123* en ionos
            $this ->_conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            //echo "<script>console.log('Conexión exitosa');</script>";
        } catch (PDOException $e){
            echo "Error ".$e->getMessage();
        }
        return $this->_conn;
    }
}
   

/*Nombre de host: db5017548489.hosting-data.io
Puerto: 3306
bbdd: dbs14058701
Nombre de usuario: dbu283137
Tipo y versión: MySQL 8.0*/