<?php

//CLASSE UPLOAD DE ARQUIVOS

class procUpload{

    private $defaultImage;
    private $extensionsPermitted;

    //METODO CONSTRUTOR DA CLASSE PROCUPLOAD
    public function __construct()
    {
        $this->extensionsPermitted            = array('png','jpg','jpeg','gif','mp4');
    }

    //FUNCAO DE ENVIO DE IMAGEM DO PERFIL DO USUARIO
    public function procFile($file)
    {
        //IMAGEM PADRAO
        $image                          = "public/uploads/perfil/imagenotfound.png";
        //DIRETORIO ONDE SERA ARMAZENADO AS IMAGEM DOS PERFIL
        $uploads                        = "public/uploads/perfil/";

        //NOME DO ARQUIVO
        $file_name                      = $uploads.$file['file']['name'];
        //EXTENSAO DO NOME DO ARQUIVO
        $file_extension                 = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        //VERIFICA SE A EXTENSAO E PERMITIDA
        if (in_array($file_extension,$this->extensionsPermitted)):
            //GERA UM NOME PARA A IMAGEM NO SERVIDOR
            $token                      = md5(uniqid(mt_rand(), true));
            //NOVO NOME DO ARQUIVO
            $new_file_name              = $uploads.$token.".$file_extension";
            //MOVE O ARQUIVO PARA O DIRETORIO EXPECIFICADO EM UPLOADS
            if (move_uploaded_file($file['file']['tmp_name'], $new_file_name)):
                $image                  = $new_file_name;
            endif;
        endif;

        return $image;
    }


    //FUNCAO DE ENVIO DE IMAGEM DOS MEMES
    public function procMeme($file)
    {
        //IMAGEM PADRAO
        $image                          = "public/img/image-not-found-4a963b95bf081c3ea02923dceaeb3f8085e1a654fc54840aac61a57a60903fef";
        //DIRETORIO ONDE SERA ARMAZENADO AS IMAGEM DOS MEME
        $uploads                        = "public/uploads/meme/";

        //NOME DO ARQUIVO
        $file_name                      = $uploads.$file['file']['name'];
        //EXTENSAO DO NOME DO ARQUIVO
        $file_extension                 = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        //VERIFICA SE A EXTENSAO E PERMITIDA
        if (in_array($file_extension,$this->extensionsPermitted)):
            //GERA UM NOME PARA A IMAGEM NO SERVIDOR
            $token                      = md5(uniqid(mt_rand(), true));
            //NOVO NOME DO ARQUIVO
            $new_file_name              = $uploads.$token.".$file_extension";
            //MOVE O ARQUIVO PARA O DIRETORIO EXPECIFICADO EM UPLOADS
            if (move_uploaded_file($file['file']['tmp_name'], $new_file_name)):
                $image                  = $new_file_name;
            endif;
        endif;

        return $image;
    }
}
