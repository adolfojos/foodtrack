function customToastNotice(msg, displayLength = 5000) {
    Materialize.toast(`<i class="material-icons">check_circle</i> <span class="toast-text">${msg}</span>`, displayLength, 'green');
}

function customToastError(msg, displayLength = 5000) {
    Materialize.toast(`<i class="material-icons">cancel</i> <span class="toast-text">${msg}</span>`, displayLength, 'black');
}