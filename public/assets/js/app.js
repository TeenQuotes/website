var timeoutReference;
$(document).ready(function() {
	$('.social-buttons').delay(2000).animate({
		opacity: 1
	}, 1000);
	$('.alert').delay(3000).fadeOut(1000);
	$('input#login-signup').keypress(function() {
		var el = this;

		if (timeoutReference) clearTimeout(timeoutReference);
		timeoutReference = setTimeout(function() {
			doneTypingLoginSignup.call(el);
		}, 3000);
	});
	$('input#login-signup').blur(function() {
		doneTypingLoginSignup.call(this);
	});
});

function doneTypingLoginSignup() {
	if (!timeoutReference) {
		return;
	}
	timeoutReference = null;

	$("#login-awesome").fadeIn(500);
}