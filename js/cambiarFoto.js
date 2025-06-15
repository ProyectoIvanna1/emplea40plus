
$(document).ready(function () {
    $('#btn_cambiar_foto').on('click', function() {
        $('#nueva_foto').click();
    });
    
    $('#nueva_foto').on('change', function() {
        if (this.files[0].size > 2097152) {
            alert('El archivo es demasiado grande. Máximo 2MB');
            this.value = '';
        } else {
            this.form.submit();
        }
    });
});