jQuery(function() {
    $('#table-fsc').DataTable(optionsTable);
    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
});