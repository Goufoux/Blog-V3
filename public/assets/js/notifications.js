function notifications()
{
    $.ajax({
        type: "GET",
        url: "/public/ajax/ajax.php",
        data: "action=notifications",
        success: function(data) {
            if(data != 'false' && data != 'null' && data.length > 0) {
                if($('#notifications-body').is(':visible')) {
                    $('#notifications-body').append(data);
                    return;
                }
                $('#notifications-body').html(data);
                $('#container-notifications').fadeIn('slow');
            }
        }
    })
}