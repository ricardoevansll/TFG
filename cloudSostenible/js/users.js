$(document).ready(function() {
    $('.edit-btn').click(function() {
        const card = $(this).closest('.card');
        card.toggleClass('active');
    });

    $('.edit-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: 'update_users.php',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error al guardar: ' + response.message);
                }
            },
            error: function() {
                alert('Error en la conexión.');
            }
        });
    });

    $('.delete-btn').click(function() {
        if (confirm('¿Seguro que desea eliminar?')) {
            const id = $(this).data('id');
            const card = $(this).closest('.card');
            card.fadeOut(300, function() {
                $.post('delete_users.php', { iduser: id }, function(response) {
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

    $('#search').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.card').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});