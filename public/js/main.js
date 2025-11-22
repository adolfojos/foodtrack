function toggleInstitutionBlock() {
    const role = document.getElementById('role').value;
    const block = document.getElementById('institutionBlock');
    if (role === 'director' || role === 'vocero_institucional') {
        block.style.display = 'block';
    } else {
        block.style.display = 'none';
    }
}

// Ejecutar al cargar la página (por si ya hay un rol seleccionado)
document.addEventListener('DOMContentLoaded', toggleInstitutionBlock);
$(function () {
    // Deshabilitar botones de submit en los formularios al enviar el formulario.
    $('form').submit(function () {
        $(this).find("button[type='submit']").prop('disabled', true);
        $(this).submit(function () {
            return false;
        });
        return true;
    });

    // Deshabilitar los vínculos que tengan esta clase después del click.
    $('.click-and-disable').on('click', function (event) {
        if ($(this).hasClass('disabled')) {
            event.preventDefault();
        }
        $(this).addClass('disabled');
    });
});

$(document).ready(function () {
    $('.dropdown-button-custom').dropdown({
        inDuration: 300,
        outDuration: 225,
        constrainWidth: true, // Does not change width of dropdown to that of the activator
        hover: false, // Activate on hover
        gutter: 0, // Spacing from edge
        belowOrigin: true, // Displays dropdown below the button
        alignment: 'right', // Displays dropdown with edge aligned to the left of button
        stopPropagation: true // Stops event propagation
    });

    $('.datepicker').pickadate({
        selectMonths: true,
        selectYears: 120,
        max: new Date(),
        today: false,
        clear: false,
        close: 'Enviar',
        format: 'dd/mm/yyyy',
        monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        showMonthsShort: undefined,
        showWeekdaysFull: undefined,
        labelMonthNext: 'Mes siguiente',
        labelMonthPrev: 'Mes anterior',
        labelMonthSelect: 'Selecciona el mes',
        labelYearSelect: 'Selecciona el año'
    }).on('mousedown', function (event) {
        event.preventDefault();
    });
    $('.datepicker-clear').pickadate({
        selectMonths: true,
        selectYears: 120,
        max: new Date(),
        today: false,
        clear: 'Eliminar',
        close: 'Enviar',
        format: 'yyyy-mm-dd',
        monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        showMonthsShort: undefined,
        showWeekdaysFull: undefined,
        labelMonthNext: 'Mes siguiente',
        labelMonthPrev: 'Mes anterior',
        labelMonthSelect: 'Selecciona el mes',
        labelYearSelect: 'Selecciona el año',
    }).on('mousedown', function (event) {
        event.preventDefault();
    });

    $('.future_datepicker').pickadate({
        selectMonths: true,
        selectYears: 120,
        min: new Date(),
        today: false,
        clear: false,
        close: 'Enviar',
        format: 'dd/mm/yyyy',
        monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        showMonthsShort: undefined,
        showWeekdaysFull: undefined,
        labelMonthNext: 'Mes siguiente',
        labelMonthPrev: 'Mes anterior',
        labelMonthSelect: 'Selecciona el mes',
        labelYearSelect: 'Selecciona el año'
    }).on('mousedown', function (event) {
        event.preventDefault();
    });

    $('select').material_select();

    $('.button-collapse').on('click', function () {
        $('#slide-out').toggleClass('hide-on-med-and-down').css('transform', 'translateX(0)');
    });
});

/**
 * Establecer el cursor en determinada posición después de establecer el foco.
 * @param pos
 * @returns {$}
 */
$.fn.setCursorPosition = function (pos) {
    this.each(function (index, elem) {
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    });
    return this;
};
