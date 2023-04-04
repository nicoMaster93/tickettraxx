jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $("body").on("change", ".permisos_lv1 input[type='checkbox']", function(e) {
        $(this).parent().parent().find($("input[type='checkbox']")).prop("checked", $(this).prop("checked"));
    });

});