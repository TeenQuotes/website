$(document).ready(function() {
	var div = '<div id="need-money">' + laravel.moneyDisclaimer + '</div>';
	if (typeof(displayNeedMoney) == "undefined")
		$("#paginator-quotes").before(div);
});