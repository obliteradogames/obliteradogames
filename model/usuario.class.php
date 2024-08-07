<?php

include_once('conexao.class.php');

class usuarioModel
{
    private $conexao;

    public function __construct()
    {
        $conn 				= new Conexao();
        $this->conexao 		= $conn->getConexao();
    }
    public function login($nick,$senha)
    {

        try{
            $stmt = $this->conexao->prepare("SELECT id,perfil,nick,email,recado FROM usuario WHERE nick=:nick and senha=:senha");
            $stmt->bindValue(':nick',$nick);
            $stmt->bindValue(':senha',$senha);
            $stmt->execute();
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0){
                if(!session_status() == PHP_SESSION_NONE):
                    session_start();
                endif;
                $_SESSION['user'] = $dados;
            }
        }
        catch(Exception $e)
        {
            //echo $e->getMessage();
        }

        //header("Location:index.php");
    }

    public function cadastrar($nick,$email,$senha)
    {

        $mensagem = "Não foi possivel se cadastrar";

        $foto = "public/uploads/perfil/imagenotfound.png";
        $recado = "Sem recados no seu mural";
        try
        {
            $stmt = $this->conexao->prepare("INSERT INTO usuario VALUES(0,:foto,:nick,:email,:senha,:recado)");
            $stmt->bindValue(':nick',$nick);
            $stmt->bindValue(':foto',$foto);
            $stmt->bindValue(':email',$email);
            $stmt->bindValue(':senha',$senha);
            $stmt->bindValue(':recado',$recado);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $mensagem = "Cadastrado";
            }

        }
        catch(Exception $e)
        {
            $mensagem = $e;
        }

        return $mensagem;

    }

    // ALTERA FOTO DO PERFIL DO USUARIO
    public function __alterFoto($foto){

        include_once('proc_upload.class.php');

        // classe responsavel por upar imagem
        $proc = new procUpload();

        $id 							= $_SESSION['user']['id'];
        $img 							= $proc->procFile($foto);

        try{

            // atualiza o diretorio da imagem
            $stmt = $this->conexao->prepare("UPDATE usuario SET perfil=:foto WHERE id=:id");
            $stmt->bindValue(":foto", $img);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            // apaga a foto antiga do servidor php
            if (is_file($_SESSION['user']['perfil'])){
                unlink($_SESSION['user']['perfil']);
            }

            // seleciona o novo diretorio e passa pra sessão do usuario
            $stmt = $this->conexao->prepare("SELECT perfil FROM usuario WHERE id=:id");
            $stmt->bindValue(':id',$id);
            $stmt->execute();

            $dados 								= $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() > 0)
            {
                $_SESSION['user']['perfil'] 	= $dados['perfil'];
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
        header("Location:index.php");
    }
}
