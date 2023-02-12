jQuery(function() {
    let cargando = () => {
        if (typeof $("#cargando")[0] !== 'undefined') {
            $("#cargando").css("display", "flex");
        }
        else{
            $("body").append('<div id="cargando" style="display: flex;"><div class="loading-green"><p>Connecting to Quickbooks</p></div></div>');
        }
    }

    let countSelects = 0;
    const maxSelect = 5;
    $('#table-tickets').DataTable(optionsTable);
    
    $("input[name='select-ticket[]']").on("change", function(e){
        if($(this).is(":checked")){
            countSelects++;
        }
        else{
            countSelects--;
        }

        if(countSelects > maxSelect){
            countSelects--;
            $(this).prop("checked",false);
            alert("The maximum number of tickets you can select are: " + maxSelect);
        }
    });

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

    $("body").on("submit","#form-tickets-selected", function(e){
        e.preventDefault();
        cargando();        
        var formdata = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            cache: false,
            processData: false,
            contentType: false,
            data: formdata,
            success: function(data) {
                $("#cargando").css("display", "none");
                if (data.success) {
                    $(".resp-invoice").html(data.form);
                    $("#modal-invoice").modal("show");
                } else {
                    alert(data.message);
                }
            },
            error: function(data) {
                $("#cargando").css("display", "none");
                alert(data.message);
            }
        });
    });

    $("body").on("submit","#form-create-invoice", function(e){
        e.preventDefault();
        cargando();        
        var formdata = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            cache: false,
            processData: false,
            contentType: false,
            data: formdata,
            success: function(data) {
                $("#cargando").css("display", "none");
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            },
            error: function(data) {
                $("#cargando").css("display", "none");
                alert(data.responseJSON.message);
            }
        });
    });





    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
});