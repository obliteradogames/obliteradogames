<?php

// CONTADOR DE VISITAS



class Contador{

    private $conexao;

    public function __construct(){
        
    }

    public function verificaIP($ip){

       

        include_once('model/conexao.class.php');

        $conn = new Conexao();
        $conexao = $conn->getConexao();

        $existe = false;
        try{

            $stmt = $conexao->prepare("SELECT * FROM contador WHERE ip=:ip");
            $stmt->bindValue(":ip",$ip);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $existe = true;
            }
        }catch(PDOException $e){
            // echo $e->getMessage();
        }

        return $existe;
    }

    public function registrarIP($ip){

        include_once('model/conexao.class.php');

        $conn = new Conexao();
        $conexao = $conn->getConexao();

        $verif = $this->verificaIP($ip);
        if(!$verif){
            try{
                $stmt = $conexao->prepare("INSERT INTO contador VALUES(0,:ip)");
                $stmt->bindValue(":ip",$ip);
                $stmt->execute();
            }catch(PDOException $e){
                // echo $e->getMessage();
            }
        }

        return $verif;
    }

    // retorna o numero de acessos
    public function getAcessos(){

        include_once('model/conexao.class.php');

        $conn = new Conexao();
        $conexao = $conn->getConexao();

        $acessos = 0;

        try{
             $stmt = $conexao->prepare("SELECT * FROM contador");
             $stmt->execute();
             $acessos = $stmt->rowCount();
        }catch(PDOException $e){
             // echo $e->getMessage();
        }

        return $acessos;
    }
}
