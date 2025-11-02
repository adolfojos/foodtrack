$('#message-alert-logout').modal();
var timeLogout;
var timeMessage;
		
function clearTimes() {
    clearTimeout(timeMessage);
    clearTimeout(timeLogout);
		
    timeMessage = setTimeout(function () {
        $('#message-alert-logout').modal('open');
    }, 480000);
		
    timeLogout = setTimeout(function () {
        window.location = '/foodtrack/public/auth/logout/';
    }, 600000);
}
		
clearTimes();
		
$('#alert-accept').click(function () {
    $.get('/core/reload', function () {
        clearTimes();
    });
});