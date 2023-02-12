jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });
    
});