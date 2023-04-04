jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    
    $('#table-settlement').DataTable(optionsTable);

    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $("body").on("click",".details", function(e){
        e.preventDefault();
        $.get($(this).prop("href"))
        .done(function( data ) {
            $(".details-body").html(data.html);
            $("#detailsModal").modal("show");
        })
        .fail(function(data) {
            console.log(data);
            alert( "error" );
        });  
    });
    
});