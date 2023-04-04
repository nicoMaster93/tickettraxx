jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const cambiarTotal = (tonage, rate) => {
        
        $("#total").val(tonage*rate);
    };

    const consultaRate = () => {
        const idPickup = $("#pickup").val();
        const idDeliver = $("#deliver").val();
        if(idPickup != "" && idDeliver != ""){
            $.ajax({
                type: 'GET',
                url: "/po_codes/rate/"+idPickup+"/"+idDeliver,
                success: function(data) {
                    if(data.success){
                        $("#rate").val(data.rate);
                        cambiarTotal($("#tonage").inputmask('unmaskedvalue'), $("#rate").inputmask('unmaskedvalue'));
                    }                
                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                }
            });
        }
    }
    $("#pickup").on("change", function(e){
        consultaRate();
    });
    $("#deliver").on("change", function(e){
        consultaRate();
    });
    

    
    $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });
    $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });

    $("#tonage").change(function(e){
        e.preventDefault();
        cambiarTotal($("#tonage").inputmask('unmaskedvalue'), $("#rate").inputmask('unmaskedvalue'));        
    });

    $("#rate").change(function(e){
        e.preventDefault();
        cambiarTotal($("#tonage").inputmask('unmaskedvalue'), $("#rate").inputmask('unmaskedvalue'));        
    });


    $("body").on("change", ".select-with-other", function(e) {
        e.preventDefault();
        if($(this).val() == "other"){
            console.log($(this).val(), $(this).attr("data-id-other"));
            $(`#${$(this).attr("data-id-other")}`).parent().addClass("active");
        }
        else{
            $(`#${$(this).attr("data-id-other")}`).parent().removeClass("active");
        }
    });

    $("body").on("change", "#photo", function(e) {
        e.preventDefault();
        $("#photo_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview", "previewText");
        
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
            for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                // Si los elementos arrastrados no son ficheros, rechazarlos
                if (ev.dataTransfer.items[i].kind === 'file') {
                    var file = ev.dataTransfer.items[i].getAsFile();
                    $("#photo_box_name").val(file.name);
                    mostrarImgNew(file, "preview", "previewText");
                    addHiddenFile(file, "photo", "photo_box_data");
                    
                }
            }
        } 
        else {
            for (var i = 0; i < ev.dataTransfer.files.length; i++) {
                var file = ev.dataTransfer.files[i];
                $("#photo")[0].files[0] = file;
                mostrarImgNew(file, "preview", "previewText");
                addHiddenFile(file, "photo", "photo_box_data");
            }
        }
        
        if (ev.dataTransfer.items) {
            ev.dataTransfer.items.clear();
        } else {
            ev.dataTransfer.clearData();
        }
        $(ev.target).removeAttr("drop-active"); 
    }
    
    
});