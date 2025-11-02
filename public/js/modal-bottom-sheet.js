$('.modal.bottom-sheet').modal();

$(window).on('resize', function () {
    $('.modal.bottom-sheet').modal('close');
});