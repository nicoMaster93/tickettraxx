jQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#deduction_type").change(function(e) {
        $(".for-type-1").removeClass("active");
        $(".for-type-2").removeClass("active");
        $(".for-type-3").removeClass("active");
        $(".for-type-4").removeClass("active");

        if($(this).val() == "1"){
            $(".for-type-1").addClass("active");
        }
        else if($(this).val() == "2"){
            $(".for-type-2").addClass("active");
        }
        else if($(this).val() == "3"){
            $(".for-type-3").addClass("active");
        }
        else if($(this).val() == "4"){
            $(".for-type-4").addClass("active");
        }
        $(".fixed_value").removeClass("active");
        $(".number_installments").removeClass("active");
        $("#charge_type").val("");
    });

    const fill_vehicles = (i) => {
        return `
        <div class="row item_vehicle">
            <div class="col-md-2 vehicle">
                <div class="form-floating">
                    <input type="text" class="form-control" id="vehicle_${i}" name="vehicle_${i}" placeholder="Vehicle ${i}">
                    <label for="vehicle_${i}">Vehicle ${i}</label>
                </div>
            </div>
            <div class="col-md-2 date_vehicle">
                <div class="form-floating">
                    <input type="date" class="form-control" id="date_vehicle_${i}" name="date_vehicle_${i}" placeholder="Date Vehicle ${i}">
                    <label for="date_vehicle_${i}">Date Vehicle ${i}</label>
                </div>
            </div>
            <div class="col-md-2 date_vehicle">
                <div class="form-floating">
                    <input type="text" class="form-control" id="city_${i}" name="city_${i}" placeholder="City ${i}">
                    <label for="city_${i}">City ${i}</label>
                </div>
            </div>
            <div class="col-md-2 date_vehicle">
                <div class="form-floating">
                    <input type="text" class="form-control" id="state_${i}" name="state_${i}" placeholder="State ${i}">
                    <label for="state_${i}">State ${i}</label>
                </div>
            </div>
            <div class="col-md-2 date_vehicle">
                <div class="form-floating">
                    <input type="text" class="form-control decimal" id="gallons_${i}" name="gallons_${i}" placeholder="Gallons ${i}">
                    <label for="gallons_${i}">Gallons ${i}</label>
                </div>
            </div>
            <div class="col-md-2 date_vehicle">
                <div class="form-floating">
                    <input type="text" class="form-control currency" id="total_${i}" name="total_${i}" placeholder="Total ${i}">
                    <label for="total_${i}">Total ${i}</label>
                </div>
            </div>
        </div>
        `;
    }

    $("#charge_type").change(function(){
        $(".fixed_value").removeClass("active");
        $(".number_installments").removeClass("active");
        if($(this).val() != ""){
            $("." + $(this).val()).addClass("active");
        }
    });


    $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });
    $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });
    
    $("body").on("click", ".plus",function(e) {
        $("#vehicles").val(parseInt($("#vehicles").val()) + 1);
        const i = $("#vehicles").val();
        const element = fill_vehicles(i);
        $("#cont-vehicles").append(element);
        $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });
        $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });

        
    });

    $("body").on("click", ".minus",function(e) {
        $("#vehicles").val(parseInt($("#vehicles").val()) - 1);
        if($("#vehicles").val() <= 0){
            $("#vehicles").val(1);
        }
        else{
            $(".item_vehicle").last().remove();
        }
    });

    $("body").on("change", "#vehicles",function(e) {
        if(parseInt($("#vehicles").val()) > 0){
            let num = parseInt($("#vehicles").val());
            let cant_actual = $(".item_vehicle").length;

            if(cant_actual > num){
                for (let i = cant_actual; i > num; i--) {
                    $(".item_vehicle").last().remove();
                }
            }
            else{
                for (let i = cant_actual + 1; i <= num; i++) {
                    const element = fill_vehicles(i);
                    $("#cont-vehicles").append(element);
                    $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });
                    $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });
                }
            }
        }
        else{
            $(".item_vehicle").remove();
            $("#vehicles").val(1);
            const i = $("#vehicles").val();
            const element = fill_vehicles(i);
            $("#cont-vehicles").append(element);
            $(".currency").inputmask({ alias: "currency", removeMaskOnSubmit: true });
            $(".decimal").inputmask({ alias: "decimal", removeMaskOnSubmit: true });
        }
    });

    $("#contractor").change(function(e){
        e.preventDefault();
        const idContractor = $(this).val();
        $(`#list_vehicles`).html('<option value="">List of vehicles</option>');
        if(idContractor != ""){
            $.ajax({
                type: 'GET',
                url: "/vehicles/by_contractor/"+idContractor,
                success: function(data) {
                    if(data.success){
                        for(let c in data.vehicles){
                            $(`#list_vehicles`).append(`<option value="${data.vehicles[c].id}">${data.vehicles[c].unit_number}</option>`);
                        }
                    }                
                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                }
            });
        }

    });

});