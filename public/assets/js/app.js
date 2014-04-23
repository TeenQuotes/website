var timeoutReference;
$(document).ready(function() {
	$('.social-buttons').delay(2000).animate({
		opacity: 1
	}, 1000);

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

	$('#countLetters').css("display", "none");
	$('#submit-quote, #submit-comment').attr('disabled','disabled');
	
	// Add a quote character's counter
	$('#content-quote').keyup(function() {
		var nbCaracters = $(this).val().length;
		if (nbCaracters < 50)
			var msg = '<i class="fa fa-meh-o"></i> It\'s a bit short!';
		else
			var msg = '<i class="fa fa-smile-o"></i> It\'s going to be a great quote!';

		$('#countLetters').html(msg);
		if (nbCaracters >= 50) {
			$('#countLetters').addClass("green");
			$('#countLetters').removeClass("orange");
			$('input[type="submit"]').removeAttr('disabled');
		} else {
			$('input[type="submit"]').attr('disabled','disabled');
			$('#countLetters').addClass("orange");
			$('#countLetters').removeClass("green")
		}

		if ($('#countLetters').is(":visible") && nbCaracters == 0) {
			$('#countLetters').fadeOut("slow")
		}
		if ($('#countLetters').is(":hidden") && nbCaracters >= 1) {
			$('#countLetters').fadeIn("slow")
		}
	});

	// Add a comment character's counter
	$('#content-comment').keyup(function() {
		var nbCaracters = $(this).val().length;
		if (nbCaracters < 10)
			var msg = '<i class="fa fa-meh-o"></i> It\'s a bit short!';
		else
			var msg = '<i class="fa fa-smile-o"></i> It seems nice!';

		$('#countLetters').html(msg);
		if (nbCaracters >= 10) {
			$('#countLetters').addClass("green");
			$('#countLetters').removeClass("orange");
			$('input[type="submit"]').removeAttr('disabled');
		} else {
			$('input[type="submit"]').attr('disabled','disabled');
			$('#countLetters').addClass("orange");
			$('#countLetters').removeClass("green")
		}

		if ($('#countLetters').is(":visible") && nbCaracters == 0) {
			$('#countLetters').fadeOut("slow")
		}
		if ($('#countLetters').is(":hidden") && nbCaracters >= 1) {
			$('#countLetters').fadeIn("slow")
		}
	});
});

// Auto remove for alerts after 5s
window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 5000);

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