jQuery(function() {
    $('#table-contractors').DataTable(optionsTable);
    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        // debugger
        if($(this).hasClass('per')){
            $('.my-0').text('Do you really want to make delete this contractor?');
            $('#btnSubmit').val('Make Delete');
        }else{
            $('.my-0').text('Do you really want to make inactive this contractor?');
            $('#btnSubmit').val('Make Inactive');
        }
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });    
    
});