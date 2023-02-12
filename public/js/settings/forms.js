
jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });    
    
});
