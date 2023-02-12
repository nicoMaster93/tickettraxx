jQuery(function() {
    $('[data-toggle="tooltip"]').tooltip()
    $('#table-tickets').DataTable(optionsTable);
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-details").modal("hide");
        $(".modal-delete").modal("show");
    });
    $("body").on("click", ".update", function(e) {
        e.preventDefault();
        window.location.href = $(this).data('update')
    });

    $("body").on("keyup","#message", function(){
        $(".n-caract").html($(this).val().length);
    });
    
    $("body").on("click",".detail-ticket", function(){
        $.get($(this).attr("href"), function(data) {
            let ticket = data.ticket;

            $(`.btn.update`).data("update",'/tickets/update/' + ticket.id );
            $(`#form-delete`).data("action",'/tickets/delete/' + ticket.id );
            $("#info-date").html(ticket.date_gen);
            $("#info-number").html(ticket.number);
            $("#info-unit").html(ticket.vehicle.unit_number);
            $("#info-material").html(ticket.material,name);
            $("#info-tonage").html(ticket.tonage);
            $("#info-rate").html(ticket.rate);
            $("#info-total").html(ticket.total);
            $("#info-pickup").html(ticket.pickup);
            $("#info-deliver").html(ticket.deliver);

            $(".recheck-link").prop("href",ticket.recheck_link);
            $("#id-approve").val(ticket.id);
            $("#id-denied").val(ticket.id);

            $("#info-img-preview").removeClass("activo");
            $("#info-file-preview").removeClass("activo");

            if(ticket.image_base_64 != "") {
                $("#info-link-preview").prop("href",ticket.file_url);
                $("#info-img-preview").prop("src", ticket.image_base_64);
                $("#info-img-preview").addClass("activo");
            }
            else{
                const file_name_arr = ticket.file_url.split("/");
                const file_name = file_name_arr[file_name_arr.length - 1];

                $("#info-file-preview").html(file_name);
                $("#info-link-preview").prop("href",ticket.file_url);
                $("#info-file-preview").addClass("activo");
            }
            
            
            
            

        });
    });
    $("body").on("submit", "#form-delete", function(e) {
        e.preventDefault();
        var formdata = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).data("action"),
            cache: false,
            processData: false,
            contentType: false,
            data: formdata,
            success: function(data) {
                if (data.success) {
                    $("#msjResponse").append("<div class='response-success padding'>"+data.message+"<button class='close-response'></button></div>");
                    $('#form-delete :input').filter( function(){ return this.value == 'Delete'; }
                    ).css('display','none');
                    $('.modal.fade.modal-delete').on('hidden.bs.modal', function () {
                      setTimeout(() => {
                        window.location.reload()
                      }, 500);  
                    })
                } else {
                    $("#form-delete").append("<div class='response-error padding'>"+data.message+"<button class='close-response'></button></div>");
                }
            },
            error: function(data) {
                $(".response-content").append("<div class='response-error'>Error server</div>");
            }
        });
    });
    /* Limpio el boton de eliminar  */
    $('.modal.fade.modal-delete').on('hidden.bs.modal', function () {
        $('#form-delete :input').filter( function(){ return this.value == 'Delete' } ).css('display','block'); 
        $('#msjResponse').empty();
        
    })
   
    $("body").on("change", "#files", function(e) {
        e.preventDefault();
        const file = $(this)[0].files[0];
        uploadFile(file);        
    });

    $("body").on("click", ".upload-btn", function(e) {
        e.preventDefault();
        let input = $(this).attr("data-input");
        $("#"+input).trigger("click");        
    });
    
    $("body").on("dragover", ".upload-box", function(ev) {
        $(ev.target).attr("drop-active", true);
        ev.preventDefault();       
    });

    $("body").on("dragleave", ".upload-box", function(ev) {
        $(ev.target).removeAttr("drop-active");
        ev.preventDefault();       
    });

    document.getElementsByClassName('upload-box')[0].addEventListener('drop', handleDrop, false);

    function handleDrop(ev) {
        ev.preventDefault();
        if (ev.dataTransfer.items) {
            for (let i = 0; i < ev.dataTransfer.items.length; i++) {
                // Si los elementos arrastrados no son ficheros, rechazarlos
                if (ev.dataTransfer.items[i].kind === 'file') {
                    const file = ev.dataTransfer.items[i].getAsFile();
                    uploadFile(file);
                }
            }
        } 
        else {
            for (let i = 0; i < ev.dataTransfer.files.length; i++) {
                const file = ev.dataTransfer.files[i];
                uploadFile(file);
            }
        }
        
        if (ev.dataTransfer.items) {
            ev.dataTransfer.items.clear();
        } else {
            ev.dataTransfer.clearData();
        }
        $(ev.target).removeAttr("drop-active"); 
    }

    const uploadFile = function(file){
        
        console.log(file);
        
        let reader = new FileReader();    
        reader.onloadend = function() {
            console.log(reader.result);
            $("#file64").val(reader.result);
            $("#form-upload").submit();
        }
        if (file) {
            reader.readAsDataURL(file);
        } 
    }
         
    $("body").on("click", ".close-response", function(e) {
        $(this).parent().remove();
    });

    $("body").on("submit", "#form-upload", function(e) {
        e.preventDefault();

        $(".progress-content").addClass("active");
        var formdata = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            cache: false,
            processData: false,
            contentType: false,
            data: formdata,
            success: function(data) {
                $(".progress-content").removeClass("active");
                if (data.success) {
                    $(".response-content").append("<div class='response-success'>"+data.message+"<button class='close-response'></button></div>");
                } else {
                    $(".response-content").append("<div class='response-error'>"+data.message+"<button class='close-response'></button></div>");
                }
            },
            error: function(data) {
                $(".response-content").append("<div class='response-error'>Error server</div>");
            }
        });
    });
    

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
});