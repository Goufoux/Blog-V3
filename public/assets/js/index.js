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