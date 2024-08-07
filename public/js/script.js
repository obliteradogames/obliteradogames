const load_page = (page) => {
    $.ajax({
        url: page,
        success: (content) => {
            $("#content").html(content)
        }
    })
}
