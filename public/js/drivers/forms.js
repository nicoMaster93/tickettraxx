jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $("body").on("change", ".select-with-other", function(e) {
        e.preventDefault();

        console.log($(this).val());
        if($(this).val() == "other"){
            console.log($(this).val(), $(this).attr("data-id-other"));
            $(`#${$(this).attr("data-id-other")}`).parent().addClass("active");
        }
        else{
            $(`#${$(this).attr("data-id-other")}`).parent().removeClass("active");
        }
    });

    $("body").on("change",".find-cities",function(e){
        e.preventDefault();
        
        const select_city = $(this).attr("data-select-city-id");
        $(`#${select_city}`).html('<option value="">Select one</option>');
        $.ajax({
            type: 'GET',
            url: "/location/cities_by_state/" + $(this).val(),
            success: function(data) {
                console.log(data);
                if(data.success){
                    for(let c in data.cities){
                        $(`#${select_city}`).append(`<option value="${data.cities[c].id}">${data.cities[c].location_name}</option>`);
                    }
                }                
                $(`#${select_city}`).append('<option value="other">Other</option>');
            },
            error: function(data) {
                console.log("error");
                console.log(data);
            }
        });
    });

    $("body").on("change", "#photo_cdl", function(e) {
        e.preventDefault();
        $("#photo_cdl_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_cdl", "preview_cdl_text");
    });

    $("body").on("change", "#photo_medical_card", function(e) {
        e.preventDefault();
        $("#photo_medical_card_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_medical_card", "preview_medical_card_text");
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

    document.getElementById('box_cdl').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_cdl", "photo_cdl_box_data", "photo_cdl_box_name", "preview_cdl", "preview_cdl_text");
    }, false);

    document.getElementById('box_medical_card').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_medical_card", "photo_medical_card_box_data", "photo_medical_card_box_name", "preview_medical_card", "preview_medical_card_text");
    }, false);

    const handleDrop = (ev, input, data, name, preview, previewText) => {
        ev.preventDefault();
        if (ev.dataTransfer.items) {
            for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                // Si los elementos arrastrados no son ficheros, rechazarlos
                if (ev.dataTransfer.items[i].kind === 'file') {
                    var file = ev.dataTransfer.items[i].getAsFile();
                    $("#" + name).val(file.name);
                    mostrarImgNew(file, preview, previewText);
                    addHiddenFile(file, input, data);                    
                }
            }
        } 
        else {
            for (var i = 0; i < ev.dataTransfer.files.length; i++) {
                var file = ev.dataTransfer.files[i];
                $("#" + input)[0].files[0] = file;
                mostrarImgNew(file, preview, previewText);
                addHiddenFile(file, input, data);
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