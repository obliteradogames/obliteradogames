$(document).ready(function(){
    $(".foto").dblclick(function(){
        $.ajax({
            url: "view/alterarPerfil.php",
            success: (content) => {
               $(".foto").html(content);
            }
        })
    })
})
