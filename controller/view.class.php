<?php

// controla as views

class viewController
{

    public function __construct(){
        include_once('view/template/template.php');
    }

    public function index(){
        echo "<script>load_page('index.php?controller=post&action=viewPost');</script>";
        require('model/contador.class.php');
        $ip = $_SERVER['REMOTE_ADDR'];
        $contador = new Contador();
        $contador->registrarIP($ip);
    }
}

?>
