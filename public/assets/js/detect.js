$(document).ready(function() {
	var div = '<div id="need-money">' + laravel.moneyDisclaimer + '</div>';
	if (typeof(displayNeedMoney) == "undefined") {
		$("#paginator-quotes").before(div);
		ga('send', 'event', 'ads.quotes.index', 'hidden');
	} else {
		ga('send', 'event', 'ads.quotes.index', 'displayed');
	}
});