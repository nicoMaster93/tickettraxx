jQuery(function() {
    $('#table-tickets').DataTable(optionsTable);
    
    $("body").on("click", ".delete", function(e) {
        e.preventDefault();
        $("#form-delete").prop("action", $(this).prop("href"));
        $(".modal-delete").modal("show");
    });

    $("body").on("keyup","#message", function(){
        $(".n-caract").html($(this).val().length);
    });
    
    
   
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