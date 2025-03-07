document.addEventListener("DOMContentLoaded", function() {
    console.log("Script cargado correctamente.");
});

// Popup de Cookies
document.addEventListener('DOMContentLoaded', function () {
    const cookiePopup = document.getElementById('cookie-popup');
    const acceptCookiesButton = document.getElementById('accept-cookies');

    // Verificar si el usuario ya aceptó las cookies
    if (!localStorage.getItem('cookies-accepted')) {
        // Mostrar el popup con una animación
        setTimeout(() => {
            cookiePopup.classList.add('visible');
        }, 1000); // Esperar 1 segundo antes de mostrar el popup
    }

    // Ocultar el popup al hacer clic en "Aceptar"
    acceptCookiesButton.addEventListener('click', function () {
        localStorage.setItem('cookies-accepted', 'true');
        cookiePopup.classList.remove('visible'); // Ocultar con animación
        setTimeout(() => {
            cookiePopup.style.display = 'none'; // Ocultar completamente después de la animación
        }, 300); // Esperar a que termine la animación (0.3s)
    });
});
