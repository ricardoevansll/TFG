$(document).ready(function() {
    // Edición en línea
    $('.edit-btn').click(function() {
        const card = $(this).closest('.card');
        card.toggleClass('active');
    });

    // Guardar cambios vía AJAX
    $('.edit-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: 'update_resource.php',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload(); // Recargar para reflejar cambios
                } else {
                    alert('Error al guardar: ' + response.message);
                }
            },
            error: function() {
                alert('Error en la conexión.');
            }
        });
    });

    // Eliminar con animación
    $('.delete-btn').click(function() {
        if (confirm('¿Seguro que desea eliminar?')) {
            const id = $(this).data('id');
            const card = $(this).closest('.card');
            card.fadeOut(300, function() {
                $.post('delete_resource.php', { id_recurso: id }, function(response) {
                    if (response.success) {
                        card.remove();
                    } else {
                        alert('Error al eliminar: ' + response.message);
                        card.fadeIn();
                    }
                }, 'json');
            });
        }
    });

    // Búsqueda dinámica
    $('#search').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.card').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});