$(document).ready(function() {
	adblock = typeof(window.google_jobrunner) != 'object';
	var div = '<div id="need-money">' + laravel.moneyDisclaimer + '</div>';
	if (adblock)
		$("#paginator-quotes").before(div);
});