

function positivar(page,id){
    $.ajax({
        url: page,
        success: (content) => {
            $("#pos"+id).html(content);
        }
    })
}

function negativar(page,id){
    $.ajax({
        url: page,
        success: (content) => {
            $("#neg"+id).html(content)
        }
    })
}

// nova função de avaliação
function avaliar(idpost,avaliacao){
    alert(idpost+" - "+avaliacao)
    $.ajax({
        url:"index.php?controller=post&action=avaliar&idpost="+idpost+"&avaliacao="+avaliacao,
        success: (content) => {
            if(avaliacao == 1){
                $("#pos"+idpost).html(content)
            }else{
                $("#neg"+idpost).html(content)
            }
        }
    })
}

function pos(idpost){
    positivar("index.php?controller=post&action=positivar&idpost="+idpost,idpost)
}

function neg(idpost){
    negativar("index.php?controller=post&action=negativar&idpost="+idpost,idpost)
}


// mostrar comentarios

function visualizar(idpost){

    // index.php?controller=post&action=post&idpost={{idpost}}
    $("#comentarios"+idpost).slideDown(1000)
    $.ajax({
        url: "index.php?controller=post&action=post&idpost="+idpost,
        success: (content) => {
            $("#content").html(content);
            $("#comentario").css({"display":"block"})
            $(".abrir-comentarios").css({"display":"none"})
        }
    })

}

function comentar(idpost){
    $comentario = $("#comentarioTextarea").val();
    $.ajax({
        url: "index.php?controller=post&action=comentar&idpost="+idpost+"&c="+$comentario,
        success: (content) => {
            $("#comentario").html(content);
        }
    })
}
