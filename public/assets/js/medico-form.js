function soloLetrasFormularioMedico(valor) {
    return valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '').replace(/\s+/g, ' ');
}

function soloNumerosFormularioMedico(valor) {
    return valor.replace(/\D/g, '');
}

function formatoTelefonoFormularioMedico(valor) {
    valor = soloNumerosFormularioMedico(valor).slice(0, 8);
    if (valor.length > 4) {
        return valor.slice(0, 4) + '-' + valor.slice(4);
    }
    return valor;
}

document.addEventListener('DOMContentLoaded', function () {
    const nombre = document.getElementById('nombre');
    const apellido = document.getElementById('apellido');
    const especialidad = document.getElementById('especialidad');
    const telefono = document.getElementById('telefono');

    if (nombre) {
        nombre.addEventListener('input', function () {
            this.value = soloLetrasFormularioMedico(this.value);
        });
    }

    if (apellido) {
        apellido.addEventListener('input', function () {
            this.value = soloLetrasFormularioMedico(this.value);
        });
    }

    if (especialidad) {
        especialidad.addEventListener('input', function () {
            this.value = soloLetrasFormularioMedico(this.value);
        });
    }

    if (telefono) {
        telefono.addEventListener('input', function () {
            this.value = formatoTelefonoFormularioMedico(this.value);
        });
    }
});
