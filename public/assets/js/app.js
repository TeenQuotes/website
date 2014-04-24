var timeoutReference;
$(document).ready(function() {
	$('.social-buttons').delay(500).animate({
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
	$('#submit-quote, #submit-comment').attr('disabled', 'disabled');

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
			$('input[type="submit"]').attr('disabled', 'disabled');
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
			$('input[type="submit"]').attr('disabled', 'disabled');
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

	// Favorite / Unfavorite
	$('button.favorite-action').click(function() {
		var otherType, otherIcon, url, type, id_quote, iconValidation;
		url = $(this).attr('data-url');
		type = $(this).attr('data-type');
		id_quote = $(this).attr('data-id');

		$.ajax({
			type: 'post',
			cache: false,
			url: $(this).attr('data-url'),
			dataType: 'json',
			data: {},
			success: function(data) {
				// Errors
				if (data.success == false) {
					var arr = data.errors;
					$.each(arr, function(index, value) {
						if (value.length != 0) {
							$("#validation-errors").append('<div class="alert alert-error"><strong>' + value + '</strong><div>');
						}
					});
				// Success
				} else {
					if (type == "favorite") {
						otherType = "unfavorite";
						otherIcon = "fa-heart-o";
						iconValidation = "fa-thumbs-up green";
					} else {
						otherType = "favorite";
						otherIcon = "fa-heart";
						iconValidation = "fa-times red";
					}


					$(".favorite-action[data-id=" + id_quote + "]").attr('data-url', url.replace(type, otherType));
					$(".favorite-action[data-id=" + id_quote + "]").attr('data-type', otherType);

					$(".favorite-action[data-id=" + id_quote + "]").hide().html("<span class='hide_this' data-id=" + id_quote + "><i class='fa " + iconValidation + "'></i></span><span class='show_this' data-id=" + id_quote + "><i class='fa " + otherIcon + "'></i></span>").fadeIn(1000);
					$(".favorite-action[data-id=" + id_quote + "]").css("opacity", "0.5");
					$(".show_this[data-id=" + id_quote + "]").hide().delay(3000).fadeIn(1000);
					$(".hide_this[data-id=" + id_quote + "]").delay(2000).fadeOut(1000)
				}
			},
			error: function(xhr, textStatus, thrownError) {
				console.log(xhr);
				console.log(textStatus);
				console.log(thrownError);
				console.log(xhr.responseText);
				alert('Something went to wrong. Please try again later.');
			}
		});

		return false;
	});
});

// Auto remove for alerts after 5s
window.setTimeout(function() {
	$(".alert").fadeTo(500, 0).slideUp(500, function() {
		$(this).remove();
	});
}, 5000);

function doneTypingLoginSignup() {
	if (!timeoutReference) {
		return;
	}
	timeoutReference = null;

	$("#login-awesome span").text($('input#login-signup').val() + "? ");
	$("#login-awesome").fadeIn(500);
}

function doneTypingLoginPassword() {
	if (!timeoutReference) {
		return;
	}
	timeoutReference = null;

	$("#submit-form").removeClass("animated fadeInUp").addClass("animated shake");
}