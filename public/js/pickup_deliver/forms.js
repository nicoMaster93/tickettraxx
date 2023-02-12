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
});