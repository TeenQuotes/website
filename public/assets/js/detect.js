$(document).ready(function() {
	adblock = $('#ad-footer').height() === 0;
	var div = '<div id="need-money">' + laravel.moneyDisclaimer + '</div>';
	if (adblock)
		$("#paginator-quotes").before(div);
});