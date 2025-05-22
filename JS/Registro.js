document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registroForm');
    if (!form) {
        console.error('El formulario de registro no se encontró en el DOM.');
        return;
    }

    const nombreUsuarioInput = document.getElementById('nombre_usuario');
    const emailInput = document.getElementById('email');
    const contrasenaInput = document.getElementById('contrasena');
    const confirmarContrasenaInput = document.getElementById('confirmar_contrasena');

    const errorNombreUsuario = document.getElementById('error_nombre_usuario');
    const errorEmail = document.getElementById('error_email');
    const errorContrasena = document.getElementById('error_contrasena');
    const errorConfirmarContrasena = document.getElementById('error_confirmar_contrasena');

    // Función para mostrar errores
    function mostrarError(inputElement, errorElement, mensaje) {
        if (errorElement) {
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block'; // Asegúrate que el small tag sea visible
        }
        if (inputElement) {
            inputElement.classList.add('input-error'); 
        }
    }

    // Función para limpiar errores
    function limpiarError(inputElement, errorElement) {
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none'; // Ocultar el small tag
        }
        if (inputElement) {
            inputElement.classList.remove('input-error'); // Opcional: remover clase de error
        }
    }

    // Función para validar email
    function esEmailValido(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    form.addEventListener('submit', function (event) {
        let esValido = true;

        // Limpiar errores previos
        limpiarError(nombreUsuarioInput, errorNombreUsuario);
        limpiarError(emailInput, errorEmail);
        limpiarError(contrasenaInput, errorContrasena);
        limpiarError(confirmarContrasenaInput, errorConfirmarContrasena);

        // Validación de Nombre Completo
        if (!nombreUsuarioInput || nombreUsuarioInput.value.trim() === '') {
            mostrarError(nombreUsuarioInput, errorNombreUsuario, 'El nombre completo es obligatorio.');
            esValido = false;
        }

        // Validación de Correo Electrónico
        if (!emailInput || emailInput.value.trim() === '') {
            mostrarError(emailInput, errorEmail, 'El correo electrónico es obligatorio.');
            esValido = false;
        } else if (!esEmailValido(emailInput.value.trim())) {
            mostrarError(emailInput, errorEmail, 'El formato del correo electrónico no es válido.');
            esValido = false;
        }

        // Validación de Contraseña
        if (!contrasenaInput || contrasenaInput.value === '') { 
            mostrarError(contrasenaInput, errorContrasena, 'La contraseña es obligatoria.');
            esValido = false;
        } else if (contrasenaInput.value.length < 8) {
            mostrarError(contrasenaInput, errorContrasena, 'La contraseña debe tener al menos 8 caracteres.');
            esValido = false;
        }

        // Validación de Confirmar Contraseña
        if (!confirmarContrasenaInput || confirmarContrasenaInput.value === '') {
            mostrarError(confirmarContrasenaInput, errorConfirmarContrasena, 'Debes confirmar la contraseña.');
            esValido = false;
        } else if (contrasenaInput && confirmarContrasenaInput.value !== contrasenaInput.value) {
            mostrarError(confirmarContrasenaInput, errorConfirmarContrasena, 'Las contraseñas no coinciden.');
            esValido = false;
        }

        if (!esValido) {
            event.preventDefault(); // Evitar el envío del formulario si hay errores
        }
    });
});