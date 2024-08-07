<?php

//CLASSE CONEXÃO

class Conexao{

    private $host                   = "mysql:host=127.0.0.1;dbname=memeki";
    private $user                   = "root";
    private $pass                   = "";
    private $pdo                    = null;

    /*private $host                   = "mysql:host=sql208.epizy.com;dbname=epiz_26099693_memeki";
    private $user                   = "epiz_26099693";
    private $pass                   = "U73iTnWQhxDCEP";
    private $pdo                    = null;*/

    //RETORNA CONEXÃO
    public function getConexao()
    {
        try{
            $this->pdo = new PDO($this->host,$this->user,$this->pass);
        }catch(PDOException $e){

        }

        return $this->pdo;
    }
}
