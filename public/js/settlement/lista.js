jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    optionsTable["columnDefs"] = [
        { targets: "_all", orderable: false}        
    ];
    optionsTable["order"] = [
        [0, 'asc']
    ];    
    $('#table-settlement').DataTable(optionsTable);
    $('#table-settlement-out-date').DataTable(optionsTable);
    $('#table-settlement-upcoming').DataTable(optionsTable);
    

    $("body").on("click",".details-fuel", function(e){
        e.preventDefault();
        if($(this).hasClass("active")){
            $(".table_details_fuel[data-id='"+$(this).attr("data-id")+"']").removeClass("active");
            $(this).html("Details");
            $(this).removeClass("active");
        }
        else{
            $(".table_details_fuel[data-id='"+$(this).attr("data-id")+"']").addClass("active");
            $(this).html("Hide");
            $(this).addClass("active");
        }        
    });


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

    $("#select-all").change(function(e){
        $("#table-settlement input[type='checkbox']").prop("checked", $(this).is(":checked"));
    });
    $("#select-all-out-date").change(function(e){
        $("#table-settlement-out-date input[type='checkbox']").prop("checked", $(this).is(":checked"));
    });
    $("#select-all-upcoming").change(function(e){
        $("#table-settlement-upcoming input[type='checkbox']").prop("checked", $(this).is(":checked"));
    });

    $("body").on("click","#send-liquidate", function(e){
        $("#payment_date_hidden").val($("#payment_date").val());
        $("#form-settlements").submit();
    })

    $('#table-settlement').on( 'page.dt', function () {
        $("#select-all").prop("checked",false);        
    });
    $('#table-settlement-out-date').on( 'page.dt', function () {
        $("#select-all-out-date").prop("checked",false);        
    });
    $('#table-settlement-upcoming').on( 'page.dt', function () {
        $("#select-all-upcoming").prop("checked",false);        
    });
});