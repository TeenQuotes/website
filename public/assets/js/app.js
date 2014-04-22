var timeoutReference;
$(document).ready(function() {
	$('.social-buttons').delay(2000).animate({
		opacity: 1
	}, 1000);
	$('.alert').delay(3000).fadeOut(1000);

	// Signup view
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

	// Signup and signin view
	$('input#password').keypress(function() {
		var el = this;

		if (timeoutReference) clearTimeout(timeoutReference);
		timeoutReference = setTimeout(function() {
			doneTypingLoginPassword.call(el);
		}, 2000);
	});
	$('input#password').blur(function() {
		doneTypingLoginPassword.call(this);
	});

	// Signin view
	$("#listener-wants-account").mouseenter(function() {
		$("#wants-account").addClass("animated shake");
	})
	.mouseleave(function() {
		$("#wants-account").removeClass("animated shake");
	});
});

function doneTypingLoginSignup() {
	if (!timeoutReference) {
		return;
	}
	timeoutReference = null;

	$("#login-awesome span").text($('input#login-signup').val() + "? " + $("#login-awesome span").text());
	$("#login-awesome").fadeIn(500);
}

function doneTypingLoginPassword() {
	if (!timeoutReference) {
		return;
	}
	timeoutReference = null;

	$("#submit-form").removeClass("animated fadeInUp").addClass("animated shake");
}