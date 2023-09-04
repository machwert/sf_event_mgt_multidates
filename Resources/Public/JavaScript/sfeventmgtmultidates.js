$(document).ready(function(){
    var startdateHidden = $('#startdatetime-hidden').val();
    if (startdateHidden != '') {
        setActiveStartdate(startdateHidden);
    }
    $('.startdate-button').click(function(){
        var startdate = $(this).data('startdate');
        $('#startdatetime-hidden').val(startdate);
        setActiveStartdate(startdate);
    });
});

function setActiveStartdate(startdate) {
    $('.startdate-button').removeClass('active');
    $('.startdate-button').each(function () {
        if ( $(this).data('startdate') === startdate ) {
            $(this).addClass('active');
        }
    });
}