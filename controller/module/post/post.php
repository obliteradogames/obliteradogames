<?php

// funcionalidades de post.class.php

class postModule{

    public function __construct(){

    }

    // retorna um video se video, ou imagem se imagem
    // recebe como parametro uma url do arquivo a ser mostrado
    public function video_ou_imagem($url_file){

        $html = "";

        if(explode(".",$url_file)[1] == "mp4"){
            $html = '<video width=100% height=400 controls>
            <source src="'.$url_file.'" type="video/mp4">
            </video>';
        }else{
            $html = '<img src="'.$url_file.'" title="meme de memeki">';
        }

        return $html;
    }
}
