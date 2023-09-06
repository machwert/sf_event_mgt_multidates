if (typeof $ != 'undefined') {
    $(document).ready(function () {
        var startdateHidden = $('#startdatetime-hidden').val();
        if (startdateHidden != '') {
            setActiveStartdate(startdateHidden);
        }
        $('.startdate-button').click(function () {
            var startdate = $(this).data('startdate');
            $('#startdatetime-hidden').val(startdate);
            setActiveStartdate(startdate);
        });
    });

    function setActiveStartdate(startdate) {
        $('.startdate-button').removeClass('active');
        $('.startdate-button').each(function () {
            if ($(this).data('startdate') === startdate) {
                $(this).addClass('active');
            }
        });
    }
} else {
    alert('jQuery is missing. Please load it e.g. by setting TypoScript constant plugin.tx_sfeventmgt_mulitdates.loadjQuery = 1.');
}