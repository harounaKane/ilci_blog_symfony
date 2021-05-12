
$('.like').click( function () {
    let id  = $(this).attr('value');
    console.log( id )
    $.ajax({
        url: "/commentaire/likeCommentaire/" + id,
        type: "GET",
        data: {id: id},
        success: function (resultat){
            let res = JSON.parse(resultat);
            $("#"+id).html(res)
        }
    });

})
