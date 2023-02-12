jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    

    $("body").on("click", ".plus",function(e) {
        $("#num_alias").val(parseInt($("#num_alias").val()) + 1);
        const i = $("#num_alias").val();
        const element = `
        <div class="col-md-3 alias">
            <div class="form-floating">
                <input type="text" class="form-control" id="alias_${i}" name="alias_${i}" placeholder="Alias ${i}" required>
                <label for="alias_${i}">Alias ${i}</label>
            </div>
        </div>`;
        $("#cont_alias").append(element);

    });

    $("body").on("click", ".minus",function(e) {
        $("#num_alias").val(parseInt($("#num_alias").val()) - 1);
        if($("#num_alias").val() <= 0){
            $("#num_alias").val(1);
        }
        else{
            $(".alias").last().remove();
        }
    });

    $("body").on("change", "#num_alias",function(e) {
        if(parseInt($("#num_alias").val()) > 0){
            let num = parseInt($("#num_alias").val());
            let cant_actual = $(".alias").length;

            if(cant_actual > num){
                for (let i = cant_actual; i > num; i--) {
                    $(".alias").last().remove();
                }
            }
            else{
                for (let i = cant_actual + 1; i <= num; i++) {
                    const element = `
                    <div class="col-md-3 alias">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="alias_${i}" name="alias_${i}" placeholder="Alias ${i}" required>
                            <label for="alias_${i}">Alias ${i}</label>
                        </div>
                    </div>`;
                    $("#cont_alias").append(element);
                }
            }
        }
        else{
            $(".alias").remove();
            $("#num_alias").val(1);
            const i = $("#num_alias").val();
            const element = `
            <div class="col-md-3 alias">
                <div class="form-floating">
                    <input type="text" class="form-control" id="alias_${i}" name="alias_${i}" placeholder="Alias ${i}" required>
                    <label for="alias_${i}">Alias ${i}</label>
                </div>
            </div>`;
            $("#cont_alias").append(element);
        }
    });

    $("body").on("change", "#photo_truck_dot_inspection", function(e) {
        e.preventDefault();
        $("#photo_truck_dot_inspection_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_truck_dot_inspection", "preview_truck_dot_inspection_text");
    });

    $("body").on("change", "#photo_truck_registration", function(e) {
        e.preventDefault();
        $("#photo_truck_registration_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_truck_registration", "preview_truck_registration_text");
    });

    $("body").on("change", "#photo_trailer_dot_inspection", function(e) {
        e.preventDefault();
        $("#photo_trailer_dot_inspection_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_trailer_dot_inspection", "preview_trailer_dot_inspection_text");
    });

    $("body").on("change", "#photo_trailer_registration", function(e) {
        e.preventDefault();
        $("#photo_trailer_registration_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_trailer_registration", "preview_trailer_registration_text");
    });

    $("body").on("change", "#photo_trailer_over", function(e) {
        e.preventDefault();
        $("#photo_trailer_over_box_data").val("");
        var file = $(this)[0].files[0];
        mostrarImgNew(file, "preview_trailer_over", "preview_trailer_over_text");
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

    document.getElementById('box_truck_dot_inspection').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_truck_dot_inspection", "photo_truck_dot_inspection_box_data", "photo_truck_dot_inspection_box_name", "preview_truck_dot_inspection", "preview_truck_dot_inspection_text");
    }, false);

    document.getElementById('box_truck_registration').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_truck_registration", "photo_truck_registration_box_data", "photo_truck_registration_box_name", "preview_truck_registration", "preview_truck_registration_text");
    }, false);

    document.getElementById('box_trailer_dot_inspection').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_trailer_dot_inspection", "photo_trailer_dot_inspection_box_data", "photo_trailer_dot_inspection_box_name", "preview_trailer_dot_inspection", "preview_trailer_dot_inspection_text");
    }, false);

    document.getElementById('box_trailer_registration').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_trailer_registration", "photo_trailer_registration_box_data", "photo_trailer_registration_box_name", "preview_trailer_registration", "preview_trailer_registration_text");
    }, false);

    document.getElementById('box_trailer_over').addEventListener('drop', function(ev){
        handleDrop(ev, "photo_trailer_over", "photo_trailer_over_box_data", "photo_trailer_over_box_name", "preview_trailer_over", "preview_trailer_over_text");
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