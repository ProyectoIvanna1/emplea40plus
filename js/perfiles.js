document.addEventListener('DOMContentLoaded', function () {
    function showSection(id) {
        document.querySelectorAll('.centro section').forEach(sec => {
            sec.classList.remove('active-section');
        });
        const active = document.getElementById(id);
        if (active) active.classList.add('active-section');
    }

    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
            const target = this.getAttribute('href').replace('#', '');
            showSection(target);
        });
    
    });

    //JS para el botón de cambiar foto
    const btnCambiar = document.getElementById('btn_cambiar_foto');
    const inputFoto = document.getElementById('nueva_foto');
    if (btnCambiar && inputFoto) {
        btnCambiar.addEventListener('click', function() {
            inputFoto.click();
        });
    }

    // Mostrar la primera sección por defecto
    showSection('id_perfil');
});