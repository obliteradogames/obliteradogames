<?php

// INCLUDE CONEXÃO
include_once("conexao.class.php");

// CLASSE POST
class postModel{

    // variaveis
    private $conexao;

    // construtor
    public function __construct(){
        $conn = new Conexao();
        $this->conexao = $conn->getConexao();
    }

    // cadastrar post
    public function __postar($titulo, $imagem){
        include_once("proc_upload.class.php");

        $procUpload             = new procUpload();
        $imagem                 = $procUpload->procMeme($imagem);
        $idusuario              = $_SESSION['user']['id'];
        $mensagem               = "Error ao postar";

        try{

            $stmt = $this->conexao->prepare("INSERT INTO post VALUES(0, :idusuario, :titulo, :imagem)");
            $stmt->bindValue(":idusuario",$idusuario);
            $stmt->bindValue(":titulo",$titulo);
            $stmt->bindValue(":imagem",$imagem);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $mensagem = "Postado com sucesso";
            }

        }catch(PDOException $e){
            $mensagem = $e->getMessage();
        }

        return $mensagem;
    }


    // listar post
    public function listar($inicio, $total){

        $mensagem = null;

        try{
            $stmt = $this->conexao->prepare("SELECT * FROM post ORDER BY id DESC LIMIT :inicio, :total");
            $stmt->bindValue(":inicio", $inicio,PDO::PARAM_INT);
            $stmt->bindValue(":total", $total,PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0)
                $mensagem = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }
        catch(PDOException $e)
        {
            $mensagem = $e->getMessage();
        }

        return $mensagem;
    }

    // NUMERO DE REGISTROS
    public function numDeReg()
    {
        $numDeReg = -1;
        try
        {
            $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM post");
            $stmt->execute();
            $numDeReg = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            //$numDeReg = -1;
        }

        return $numDeReg;
    }

    //AVALIACAO
    // public function avaliacao($idpost)
    // {
    //
    //     $avaliacao = [
    //         "positivo" => 0,
    //         "negativo" => 0,
    //         "id_usuario_pos" => 0,
    //         "id_usuario_neg" => 0,
    //     ];
    //
    //     try{
    //
    //         $stmt = $this->conexao->prepare("SELECT id_usuario FROM positivos WHERE id_post=:id_post");
    //         $stmt->bindValue(":id_post",$idpost);
    //         $stmt->execute();
    //         $avaliacao['id_usuario_pos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         $avaliacao["positivo"] = $stmt->rowCount();
    //
    //         $stmt = $this->conexao->prepare("SELECT id_usuario FROM negativos WHERE id_post=:id_post");
    //         $stmt->bindValue(":id_post",$idpost);
    //         $stmt->execute();
    //         $avaliacao['id_usuario_neg'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //
    //         $avaliacao["negativo"] = $stmt->rowCount();
    //
    //     }catch(PDOException $e){
    //
    //     }
    //
    //     return $avaliacao;
    //
    // }

    // nova função, retorna as curtidas
    public function pegarAvaliacoes(){

        $avaliacao = null;

        try{
            $stmt = $this->conexao->prepare("SELECT avaliacao FROM avaliacao");
            $stmt->execute();
            $avaliacao = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            // echo $e->getMessage();
        }

        return $avaliacao;
    }

    // nova função de avaliação
    public function avaliar($idpost, $idusuario, $avaliacao){

        try{
            //UPDATE avaliacao SET avaliacao=0 WHERE id_post=2 AND id_usuario=2
            $stmt = $this->conexao->prepare("SELECT * FROM avaliacao WHERE id_post=:idpost AND id_usuario=:idusuario");
            $stmt->bindValue(":idpost",$idpost,PDO::PARAM_INT);
            $stmt->bindValue(":idusuario",$idusuario,PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() <= 0){
                $stmt = $this->conexao->prepare("REPLACE INTO avaliacao (id_post,id_usuario,avaliacao) VALUES(:idpost,:idusuario,:avaliacao)");
                $stmt->bindValue(":idpost",$idpost,PDO::PARAM_INT);
                $stmt->bindValue(":idusuario",$idusuario,PDO::PARAM_INT);
                $stmt->bindValue(":avaliacao",$avaliacao,PDO::PARAM_INT);
                $stmt->execute();
            }else{
                $stmt = $this->conexao->prepare("UPDATE avaliacao SET id_post=:idpost,id_usuario=:idusuario,avaliacao=:avaliacao WHERE id_post=:idpost AND id_usuario=:idusuario");
                $stmt->bindValue(":idpost",$idpost,PDO::PARAM_INT);
                $stmt->bindValue(":idusuario",$idusuario,PDO::PARAM_INT);
                $stmt->bindValue(":avaliacao",$avaliacao,PDO::PARAM_INT);
                $stmt->bindValue(":idpost",$idpost,PDO::PARAM_INT);
                $stmt->bindValue(":idusuario",$idusuario,PDO::PARAM_INT);
                $stmt->execute();
            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }

        return $this->pegarAvaliacoes();
    }

    // nova função de leitura das avaliaçõe

    public function getAvaliacao($idpost,$idusuario){
        $avaliacao = "sem valor";

        try{
            $stmt = $this->conexao->prepare("SELECT avaliacao FROM avaliacao WHERE id_post=:idpost AND id_usuario=:idusuario");
            $stmt->bindValue(":idpost",$idpost, PDO::PARAM_INT);
            $stmt->bindValue(":idusuario",$idusuario, PDO::PARAM_INT);
            $stmt->execute();
            $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC)['avaliacao'];
        }catch(PDOException $e){
            // echo $e->getMessage();
        }

        return $avaliacao;
    }

    // //CURTIR POST
    // public function positivar($idpost,$idusuario){
    //
    //     $bool = false;
    //
    //     try{
    //
    //         $stmt = $this->conexao->prepare("SELECT id FROM positivos WHERE id_usuario=:id_usuario AND id_post=:id_post UNION SELECT id FROM negativos WHERE id_usuario=:id_usuario AND id_post=:id_post");
    //         $stmt->bindValue(":id_usuario",$idusuario, PDO::PARAM_INT);
    //         $stmt->bindValue(":id_post", $idpost, PDO::PARAM_INT);
    //         $stmt->execute();
    //
    //         if(!$stmt->rowCount() > 0){
    //             $stmt = $this->conexao->prepare("INSERT INTO positivos VALUES(0,:idpost,:idusuario,:positivo)");
    //             $stmt->bindValue(":idpost", $idpost, PDO::PARAM_INT);
    //             $stmt->bindValue(":idusuario",$idusuario, PDO::PARAM_INT);
    //             $stmt->bindValue(":positivo",1,PDO::PARAM_INT);
    //             if($stmt->execute()){
    //                 $bool = $this->avaliacao($idpost)['positivo'];
    //             }
    //         }
    //
    //     }catch(PDOException $e){
    //         $bool = $e->getMessage();
    //     }
    //
    //     return $bool;
    // }
    //
    // //DESCURTIR POST
    // public function negativar($idpost, $idusuario)
    // {
    //     $bool = false;
    //
    //     try{
    //
    //         $stmt = $this->conexao->prepare("SELECT id FROM negativos WHERE id_usuario=:id_usuario AND id_post=:id_post UNION SELECT id FROM positivos WHERE id_usuario=:id_usuario AND id_post=:id_post");
    //         $stmt->bindValue(":id_usuario",$idusuario, PDO::PARAM_INT);
    //         $stmt->bindValue(":id_post", $idpost, PDO::PARAM_INT);
    //         $stmt->execute();
    //
    //         if(!$stmt->rowCount() > 0){
    //             $stmt = $this->conexao->prepare("INSERT INTO negativos VALUES(0,:idpost,:idusuario,:negativo)");
    //             $stmt->bindValue(":idpost", $idpost, PDO::PARAM_INT);
    //             $stmt->bindValue(":idusuario",$idusuario, PDO::PARAM_INT);
    //             $stmt->bindValue(":negativo",1,PDO::PARAM_INT);
    //             if($stmt->execute()){
    //                 $bool = $this->avaliacao($idpost)['negativo'];
    //             }
    //         }
    //
    //     }catch(PDOException $e){
    //         $bool = $e->getMessage();
    //     }
    //
    //     return $bool;
    // }

    //EXCLUIR POST
    public function excluir()
    {
        echo "Ok";
    }

    // visualiza comentarios de um determinado post
    public function getComent($idpost){


        // armazena os comentarios e o id correspondente ao comentario
        $comentarios                    = array();
        // armazena o perfil, nick do id usuario do comentario de um post
        $comentario_usuario             = array();

        try{

            $stmt = $this->conexao->prepare("SELECT id_usuario,comentario FROM comentarios WHERE id_post=:id_post");
            $stmt->bindValue(':id_post', $idpost, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $comentario):
                    $comentarios[] = $comentario;
                endforeach;

                foreach ($comentarios as $value):
                    $stmt = $this->conexao->prepare("SELECT perfil, nick FROM usuario WHERE id=:id_usuario");
                    $stmt->bindValue(':id_usuario',$value['id_usuario'], PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    $comentario_usuario[] = array($results['perfil'],$results['nick'],$value['comentario']);
                endforeach;
            endif;


        }catch(PDOException $e){
            // $comentarios = $e->getMessage();
        }

        return $comentario_usuario;
    }

    // registra um comentario em um determinado post
    public function comentar($idusuario,$idpost,$conteudo){

        $bool = false;

        try{

            $stmt = $this->conexao->prepare("INSERT INTO comentarios VALUES(0,:id_usuario,:id_post,:conteudo)");
            $stmt->bindValue(':id_usuario', $idusuario, PDO::PARAM_INT);
            $stmt->bindValue(':id_post', $idpost, PDO::PARAM_INT);
            $stmt->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $bool = true;
            }

        }catch(PDOException $e){
            // $bool = $e->getMessage();
        }

        return $bool;
    }

    // pega post
    public function getPost($idpost){

        $post = null;

        try{
            $stmt = $this->conexao->prepare("SELECT * FROM post WHERE id=:idpost");
            $stmt->bindValue(":idpost", $idpost,PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $post = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $e){
            $post = $e->getMessage();
        }

        return $post;
    }

    // pega quantidade de comentarios do post

    public function getNumComent($idpost){

        $numComent = null;

        try{
            $stmt = $this->conexao->prepare("SELECT * FROM comentarios WHERE id_post=:idpost");
            $stmt->bindValue(":idpost", $idpost,PDO::PARAM_INT);
            $stmt->execute();

            $numComent = $stmt->rowCount();

        }catch(PDOException $e){
            //$post  $e->getMessage();
        }

        return $numComent;
    }
}

// echo "Post Model";
// $pm = new postModel();
// $pm->avaliar(1,2,0);
//echo $pm->getAvaliacao(1,2);
