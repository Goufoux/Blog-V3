$(document).ready(function() {
    notifications();
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('body').on('click', '.close', function() {
        let target = $(this).attr('data-close');
        if(target.length <= 0) {
            console.log("Attribut close non défini");
            return;
        }
        $('#'+target).slideUp('slow', function() {
            $('#'+target).remove();
        });
    });

    /* replace footer */
    
    $(function() {
        var body = document.body,
            html = document.documentElement;
        var wh = window.innerHeight;

        var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );

        console.log('container'+$('.container-fluid').height());

        if (wh == height) {
            console.log("add fix");
            $("footer").addClass('footer-fix');
        }
        $('footer').animate({
            'opacity': 1
        }, 750);
    });

    /* Delete image */
    $('.delete-image').on('click', function() {
        let key = $(this).attr('data-key');
        $.ajax({
            type: "GET",
            url: "/post/deleteImage",
            data: "post="+key,
            success: function (data) {
                if (data == 1) {
                    $('.previsualisation-image').fadeOut('slow', function() {
                        $('.previsualisation-image').remove();
                    });
                    $('#image_alt').val("");
                }
                notifications();
            }
        });
    });
});