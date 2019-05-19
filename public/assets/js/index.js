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

});