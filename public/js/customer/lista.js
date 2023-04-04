jQuery(function() {
    $('#table-custome').DataTable(optionsTable);
    
    // $("body").on("click", ".delete", function(e) {
    //     e.preventDefault();
    //     $("#form-delete").prop("action", $(this).prop("href"));
    //     $(".modal-delete").modal("show");
    // });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    let cargando = () => {
        if (typeof $("#cargando")[0] !== 'undefined') {
            $("#cargando").css("display", "flex");
        }
        else{
            $("body").append('<div id="cargando" style="display: flex;"><div class="loading-green"><p>Connecting to Quickbooks</p></div></div>');
        }
    }
   

    $("body").on("click",".quickbooks_login", function(e){
        e.preventDefault();
        // Launch Popup
        let url = $(this).attr("href");
        let parameters = "location=1,width=800,height=650";
        parameters += ",left=" + (screen.width - 800) / 2 + ",top=" + (screen.height - 650) / 2;
        let win = window.open(url, 'connectPopup', parameters);
        let pollOAuth = window.setInterval(function () {
            try {
                if (win.document.URL.indexOf("code") != -1) {
                    window.clearInterval(pollOAuth);
                    win.close();
                    location.reload();
                }
            } catch (e) {
                console.log(e)
            }
        }, 100);
    });

    $("body").on("click",".quickbooks_table", function(e){
        e.preventDefault();
        $(this).tooltip('hide')
        cargando();
        $.ajax({
            type: 'GET',
            url: $(this).attr("href"),
            success: function(data) {
                $("#cargando").css("display", "none");
                if(data.success){
                    $(".result-custormers").html(data.table_customer);
                    $('#table-customers').DataTable(optionsTable);
                    $(".modal-customers").modal("show");
                }
                else{
                    alert(data.message);
                    console.log(data);
                }
                
            },
            error: function(data) {
                $("#cargando").css("display", "none");
                alert(data.message);
                console.log("error");
                console.log(data);
            }
        });
    });

    
    
});