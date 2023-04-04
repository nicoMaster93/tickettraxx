jQuery(function() {
    $('#table-other-payments').DataTable(optionsTable);
    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });
    
});