const optionsTable = {
    pageLength: 50,
    buttons: ['print', { extend: 'excelHtml5', title: 'Excel' }, 'pdf', 'colvis']
}

const mostrarImgNew = function(file, idImage, idOtherTypes = null) {
    let previewImg = document.getElementById(idImage);
    let previewText = document.getElementById(idOtherTypes);
    let imageType = /image.*/;
    if(file.type.match(imageType)){
        let reader = new FileReader();    
        reader.onloadend = function() {
            console.log(reader);
            previewImg.src = reader.result;
        }
    
        if (file) {
            reader.readAsDataURL(file);
        } else {
            previewImg.src = "";
        }
        previewImg.style.display = 'block';
        previewText.style.display = 'none';
    }
    else{
        previewText.style.display = 'block';
        previewImg.style.display = 'none';
        previewText.innerHTML = file.name;
    }    
}

const addHiddenFile = function(file, inputClassic, inputHidden){
    
    document.getElementById(inputClassic).value = '';
    let input = document.getElementById(inputHidden);
    let reader = new FileReader();    
    reader.onloadend = function() {
        input.value = reader.result;
    }
    if (file) {
        reader.readAsDataURL(file);
    } 
}
