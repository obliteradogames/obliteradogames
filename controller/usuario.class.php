<?php

include_once('model/usuario.class.php');

// CLASSE USUARIO
class usuarioController{

    // variaveis
    private $model;

    // construtor
    public function __construct(){
        $this->model 				= new usuarioModel();
    }

    // login do usuario
    public function login(){

        if(!(isset($_POST['nick']) && isset($_POST['senha']))):
            echo "<script>load_page('view/login.php');</script>";
        else:

            $nick						= filter_input(INPUT_POST,'nick',FILTER_SANITIZE_SPECIAL_CHARS);
            $senha 						= filter_input(INPUT_POST,'senha',FILTER_SANITIZE_SPECIAL_CHARS);

            $this->model->login($nick,$senha);

            if(isset($_SESSION['user'])){
                echo "<script>alert('Logado com sucesso')</script>";
            }else{
                echo "<script>alert('Não foi possivel fazer login')</script>";
            }

            header("Refresh:0.5;url=index.php");
        endif;
    }


    // função cadastro do usuario
    public function cadastrar(){

        if(!(isset($_POST['nick']) && isset($_POST['senha']) && isset($_POST['email']))):
            echo "<script>load_page('view/cadastro.php');</script>";
        else:

            $erros          = array();      // armazena erros do formulario

            // variaveis do formulario filtradas e validadas
            $nick 	  		= filter_input(INPUT_POST, 'nick', FILTER_SANITIZE_SPECIAL_CHARS);
            $senha		    = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
            $email 		    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if(!$email):
                $erros[] = "Formato de E-mail ínvalido";
            else:
                $this->model->cadastrar($nick, $email, $senha);
                $this->model->login($nick, $senha);
            endif;

            // verifica se a erros no prenchimento do formulario
            if(!empty($erros)):
                foreach($erros as $erro):
                    echo "<script>alert('".$erro."')</script>";
                endforeach;
            endif;

            header("Refresh:0.5;url=index.php?controller=view&action=cadastrar");

        endif;
    }

    // perfil do usuario
    public function __perfil(){
        $arrayCampos 			= array('{{foto}}','{{nick}}','{{email}}','{{recado}}');
        $data_user 				= array(
            $_SESSION['user']['perfil'],
            $_SESSION['user']['nick'],
            $_SESSION['user']['email'],
            $_SESSION['user']['recado']
        );

        $perfil 				= file_get_contents('view/perfil.php');

        foreach($data_user as $key => $value){
            $perfil				= str_replace($arrayCampos[$key],$value,$perfil);
        }

        echo $perfil;

    }

    // alterar foto do usuario
    public function __alterFoto(){
        $foto 						= $_FILES;
        $mensagem 					= $this->model->__alterFoto($foto);
        echo $mensagem;
    }

    // sair da sessão do usuario
    public function __sair(){
        if (session_status() != PHP_SESSION_NONE):
            session_destroy();
        endif;

        header("Location:index.php");
    }

}
