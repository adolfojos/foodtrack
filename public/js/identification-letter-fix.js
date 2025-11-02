/**
 * Añadir clase identification_letter_fix para efectuar el completamiento de la letra de la cédula.
 */
$(document).ready(function () {
    $('.identification_letter_fix').on('input', function (e) {
        var username = $(this).val();
        if (username.length > 9) {
            var cedula = username.substring(2);
            if (cedula > 80000000) {
                $(this).val('E-' + cedula);
            }
        }
    });
});