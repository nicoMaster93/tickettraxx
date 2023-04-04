jQuery(function() {
    $('#table-vehicles').DataTable(optionsTable);
    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $("body").on("click", ".showDetails",function(e){
        e.preventDefault();
        $("#vehicle_id").val($(this).attr("data-id"));
        $("#detailsModal").modal("show");

    });
    
    
});