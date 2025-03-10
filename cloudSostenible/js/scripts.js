document.addEventListener("DOMContentLoaded", function() {
    console.log("Script cargado correctamente.");
});

// Popup de Cookies
document.addEventListener('DOMContentLoaded', function () {
    const cookiePopup = document.getElementById('cookie-popup');
    const acceptCookiesButton = document.getElementById('accept-cookies');

    // Verificar aceptación de cookies
    if (!localStorage.getItem('cookies-accepted')) {
        // Mostrar el popup con una animación
        setTimeout(() => {
            cookiePopup.classList.add('visible');
        }, 1000); // 1 segundo para mostrar el popup
    }

    // Ocultar el popup al hacer clic en "Aceptar"
    acceptCookiesButton.addEventListener('click', function () {
        localStorage.setItem('cookies-accepted', 'true');
        cookiePopup.classList.remove('visible'); // Ocultar con animación
        setTimeout(() => {
            cookiePopup.style.display = 'none'; // Ocultar cdespués de la animación
        }, 300); // Espera (0.3s)
    });
});
