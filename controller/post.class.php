<?php

/*
 *
 * +=================================================+
 * +          [classe que controla os post]          +
 * +=================================================+
 *
 */

include_once('model/post.class.php');

// importa modulo
require('module/post/post.php');

class postController{

    private $post;
    private $postModule; //contem metodos adicionais para post.class.php

    public function __construct(){
        $this->post             = new postModel();
        $this->post_m           = new postModule();
    }

    // cadastrar post
    public function __cadPost(){
        $post 						= file_get_contents("view/post.php");
        echo $post;
    }

    // registrar post
    public function __regPost(){

        $titulo 					= filter_input(INPUT_POST, "titulo", FILTER_SANITIZE_SPECIAL_CHARS);
        $arquivo 					= $_FILES;

        $this->post->__postar($titulo, $arquivo);

        header("Location:index.php");

    }

    // nova função de avaliar
    public function avaliar(){

        $likes              = 0;
        $deslike            = 0;

        $idpost             = $_GET['idpost'];
        $avaliacao          = $_GET['avaliacao'];
        $idusuario          = $_SESSION['user']['id'];

        $avaliacoes         = $this->post->avaliar($idpost,$idusuario,$avaliacao);

        foreach($avaliacoes as $value){
            if($value['avaliacao'] == 1){
                $likes++;
            }else{
                $deslike++;
            }
        }

        if($this->post->getAvaliacao($idpost,$idusuario) == 1){
             echo "<nav style='color:#f00;'>$likes</nav>";
        }else{
             echo "<nav>5</nav>";
        }
    }



    // visualizar posts
    public function viewPost(){
        $inicio = 0;
        $total  = 10;

        $numDeReg = $this->post->numDeReg()["COUNT(*)"];

        # isset($_GET['i']) ? $inicio += $_GET['i'];

        if(isset($_GET['i'])){
            $inicio += $_GET['i'];
        }

        if($inicio > $numDeReg){
            $inicio = 0;
        }

        $template 							= file_get_contents("view/template/meme.php");
        $posts								= file_get_contents("view/meme.php");


        ob_start();
        foreach($this->post->listar($inicio, $total) as $key => $value){

            $saida = str_replace("{{idpost}}",$value['id'], $posts);
            $saida = str_replace("{{titulo}}",$value['titulo'], $saida);

            // numero de comentarios em um post
            $numComent = $this->post->getNumComent($value['id']);

            if($numComent > 0){
                $saida = str_replace("{{numComent}}",'<span style="background:#f00;color:#fff;border-radius:5px;padding:0px 5px;">'.$numComent.'</nav>', $saida);
            }else{
                $saida = str_replace("{{numComent}}","", $saida);
            }

            // verifica se é video ou imagem
            // em seguida retorna o html do video ou imagem
            $html = $this->post_m->video_ou_imagem($value['imagem']);
            $saida = str_replace("{{file}}",$html, $saida);
            $saida = str_replace("{{file}}",$html, $saida);



            //$avaliacao = $this->post->avaliacao($value['id']);

            $positivado = false;
            // verifica se usuario está logado
            if(isset($_SESSION['user'])){

                foreach($avaliacao['id_usuario_pos'] as $key => $value){
                    if($value['id_usuario'] == $_SESSION['user']['id']){
                        $positivado = true;
                    }
                }

                // se sim, avaliado com sucesso
                if($positivado){
                    $avaliado = "<nav style='color:#ff4000;'>".$avaliacao['positivo']."</nav>";
                }else{
                    $avaliado = $avaliacao['positivo'];
                }

                $saida = str_replace("{{positivo}}",$avaliado, $saida);

            }else{
                $saida = str_replace("{{positivo}}",$avaliacao['positivo'], $saida);
            }

            $negativado = false;

            if(isset($_SESSION['user'])){

                foreach($avaliacao['id_usuario_neg'] as $key => $value){

                    if($value['id_usuario'] == $_SESSION['user']['id']){
                        $negativado = true;
                    }
                }

                // se sim, avaliado com sucesso
                if($negativado){
                    $avaliado = "<nav style='color:#f00;'>".$avaliacao['negativo']."</nav>";
                }else{
                    $avaliado = $avaliacao['negativo'];
                }

                $saida = str_replace("{{negativo}}",$avaliado, $saida);
            }else{
                $saida = str_replace("{{negativo}}",$avaliacao['negativo'], $saida);
            }

            echo $saida;
        }

        $buffer = ob_get_contents();
        ob_get_clean();

        $inicio += $total;

        $template = str_replace("{{post}}",$buffer, $template);
        $template = str_replace("{{i}}", $inicio, $template);

        // numero de acessos no site

        include('model/contador.class.php');
        $contador = new Contador();
        $acessos = $contador->getAcessos();
        $template = str_replace("{{acessos}}",$acessos, $template);

        //echo "<h2 style='color:red'>".$_SERVER['REMOTE_ADDR']."</h2>";
        echo $template;

    }


    // visualiza comentarios de um determinado post
    public function post(){

        isset($_GET['idpost']) ? $idpost = $_GET['idpost'] : $idpost = 0;
        isset($_SESSION['user']['id']) ? $iduser = $_SESSION['user']['id'] : $iduser = 0;

        // carregar visualização
        $template_meme = file_get_contents("view/template/meme.php");
        $template_post = file_get_contents("view/meme.php");

        $coments = $this->post->getComent($idpost);

        foreach($this->post->getPost($idpost) as $value){
            $saida = str_replace("{{idpost}}",$value['id'], $template_post);
            $saida = str_replace("{{titulo}}",$value['titulo'], $saida);

            // verifica se é video ou imagem
            // em seguida retorna o html do video ou imagem
            $html = $this->post_m->video_ou_imagem($value['imagem']);
            $saida = str_replace("{{file}}",$html, $saida);

        }

        $avaliacao = $this->post->avaliacao($value['id']);

        $positivado = false;
        // verifica se usuario está logado
        if(isset($_SESSION['user'])){

            foreach($avaliacao['id_usuario_pos'] as $key => $value){
                if($value['id_usuario'] == $_SESSION['user']['id']){
                    $positivado = true;
                }
            }

            // se sim, avaliado com sucesso
            if($positivado){
                $avaliado = "<nav style='color:#f00;'>".$avaliacao['positivo']."</nav>";
            }else{
                $avaliado = $avaliacao['positivo'];
            }

            $saida = str_replace("{{positivo}}",$avaliado, $saida);

        }else{
            $saida = str_replace("{{positivo}}",$avaliacao['positivo'], $saida);
        }

        $negativado = false;

        if(isset($_SESSION['user'])){

            foreach($avaliacao['id_usuario_neg'] as $key => $value){

                if($value['id_usuario'] == $_SESSION['user']['id']){
                    $negativado = true;
                }
            }

            // se sim, avaliado com sucesso
            if($negativado){
                $avaliado = "<nav style='color:#f00;'>".$avaliacao['negativo']."</nav>";
            }else{
                $avaliado = $avaliacao['negativo'];
            }

            $saida = str_replace("{{negativo}}",$avaliado, $saida);
        }else{
            $saida = str_replace("{{negativo}}",$avaliacao['negativo'], $saida);
        }

        #echo $saida;


        // carrega os comentarios

        $coments = $this->post->getComent($idpost);

        ob_start();
        echo "<nav class='coments'>";

        foreach($coments as $value){
            echo "<nav class='coment'>";
            echo "<nav class='perfil'><img src='$value[0]' width=50 height=50></nav>";
            echo "<nav class='content'>$value[2]</nav>";
            echo "</nav>";
        }

        echo '
        <nav class="form-comentario">
        <textarea id="comentarioTextarea" name="comentario"></textarea><br>
        <button name="enviar" onclick="comentar('.$idpost.')">comentar</button>
        </nav>
        ';
        echo "</nav>";
        $comentarios = ob_get_contents();
        ob_get_clean();
        $saida = str_replace("{{comentario}}", $comentarios, $saida);
        echo str_replace("{{post}}",$saida, $template_meme);
    }

    public function comentar(){

        if(isset($_GET['idpost']) && isset($_GET['c']) && !empty($_SESSION['user'])){
            $id_post = $_GET['idpost'];
            $id_user = $_SESSION['user']['id'];
            $comentario = filter_input(INPUT_GET,'c',FILTER_SANITIZE_SPECIAL_CHARS);
            $this->post->comentar($id_user, $id_post, $comentario);

            $coments = $this->post->getComent($id_post);

            echo "<nav class='coments'>";

            foreach($coments as $value){
                echo "<nav class='coment'>";
                echo "<nav class='perfil'><img src='$value[0]' width=50 height=50></nav>";
                echo "<nav class='content'>$value[2]</nav>";
                echo "</nav>";
            }

            echo '
            <nav class="form-comentario">
            <textarea id="comentarioTextarea" name="comentario"></textarea><br>
            <button name="enviar" onclick="comentar('.$id_post.')">comentar</button>
            </nav>
            ';
            echo "</nav>";

        }else{
            echo "<center><h2 style='color:red;'>Você precisa está logado para comentar nesse post</h1></center>";
        }
    }
}
