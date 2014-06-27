window.laravel = window.laravel || {};
var timeoutLoginSignup, timeoutPassword;
$(document).ready(function() {
	$('.social-buttons').delay(500).animate({
		opacity: 1
	}, 1000);

	// Signup view
	$('input#login-signup').keypress(function() {
		var el = this;

		if (timeoutLoginSignup) clearTimeout(timeoutLoginSignup);
		timeoutLoginSignup = setTimeout(function() {
			doneTypingLoginSignup.call(el);
		}, 3000);
	});
	$('input#login-signup').blur(function() {
		doneTypingLoginSignup.call(this);
	});

	// Signup and signin view
	$('input#password').keypress(function() {
		var el = this;

		if (timeoutPassword) clearTimeout(timeoutPassword);
		timeoutPassword = setTimeout(function() {
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
			var msg = '<i class="fa fa-meh-o"></i> ' + laravel.contentShortHint;
		else
			var msg = '<i class="fa fa-smile-o"></i> ' + laravel.contentGreatHint;

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

	// Search results: scrollTo
	$("#search-page .content").click(function() {
		var divName = $(this).attr('data-scroll');
		$('html, body').animate({
			scrollTop: $("#" + divName).offset().top - 80
		}, 1500);
	});


	// Add a comment character's counter
	$('#content-comment').keyup(function() {
		var nbCaracters = $(this).val().length;
		if (nbCaracters < 10)
			var msg = '<i class="fa fa-meh-o"></i> ' + laravel.contentShortHint;
		else
			var msg = '<i class="fa fa-smile-o"></i> ' + laravel.contentGreatHint;

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

	// Edit settings
	// FIXME : could be improved
	$("#edit-settings label#notification_comment_quote").html("<span class='ui'></span>" + $("#edit-settings label#notification_comment_quote").text());
	$("#edit-settings label#hide_profile").html("<span class='ui'></span>" + $("#edit-settings label#hide_profile").text());
	$("#edit-settings label#weekly_newsletter").html("<span class='ui'></span>" + $("#edit-settings label#weekly_newsletter").text());
	$("#edit-settings label#daily_newsletter").html("<span class='ui'></span>" + $("#edit-settings label#daily_newsletter").text());

	// Edit profile
	// Show upload avatar or not
	if (!hasFileUploadSupport())
		$("#change-avatar").hide();
	else
		$("#alert-change-avatar").hide();

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

					// Update counter on the user's profile
					if ($("#fav-count").length) {
						var nbFav = parseInt($("#fav-count").text());
						if (type == 'favorite')
							$("#fav-count").text(nbFav + 1);
						else {
							$("#fav-count").text(nbFav - 1);
							// Hide the quote on the profile
							$('.quote[data-id='+id_quote+']').slideUp();
						}
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

	// Attach Mailgun plugin to validate email address
	$('#email-signup').mailgun_validator({
		api_key: laravel.mailgunPubKey,
		in_progress: doNothing,
		success: validationSuccess,
		error: doNothing,
	});

	$(document).on('click', '#alternate-email', function() {
		$("#email-signup").val($(this).text());
		$('#respect-privacy').animate({
			'opacity': 0
		}, 500, function() {
			$(this).text(laravel.mailAddressUpdated);
		}).animate({
			'opacity': 1
		}, 500);
	});

	// Moderation
	$('.quote-moderation').click(function() {
		var id_quote, numberOfQuoteAwaitingMode, decision;
		id_quote = $(this).attr('data-id');
		decision = $(this).attr('data-decision');

		$.ajax({
			type: 'post',
			cache: false,
			url: $(this).attr('data-url'),
			dataType: 'json',
			data: {},
			success: function(data) {
				console.log(data);
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
					// Update nb quotes awaiting moderation
					numberOfQuoteWaitingModeration = parseInt($("#nb-quotes-waiting").text()) - 1;
					$("#nb-quotes-waiting").text(numberOfQuoteWaitingModeration);

					// Update nb quotes pending
					if (decision == 'approve') {

						numberOfQuotePending = parseInt($("#nb-quotes-pending").text()) + 1;
						$("#nb-quotes-pending").text(numberOfQuotePending);

						if ($("#text-quotes").text() == 'quote')
							$("#text-quotes").text(laravel.quotesPlural);

						nbDays = Math.floor(numberOfQuotePending / laravel.nbQuotesPerDay);
						$("#nb-quotes-per-day").text(nbDays);
						if (nbDays > 1)
							$("#text-days").text(laravel.daysPlural);
					}

					$(".quote[data-id=" + id_quote + "]").delay(100).slideUp(500);
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
	$(".alert:not(.no-hide)").fadeTo(500, 0).slideUp(500, function() {
		$(this).remove();
	});
}, 5000);

// while the lookup is performing
function doNothing() {}


// Suggest a valid email address
function validationSuccess(data) {
	var alternate = data['did_you_mean'];
	var isValid = data['is_valid'];

	if (!isValid) {
		if (alternate)
			$('#respect-privacy').html(laravel.didYouMean + "<span id='alternate-email'>" + alternate + "</span>?");
		else
			$('#respect-privacy').html('<i class="fa fa-meh-o"></i>' + laravel.mailAddressInvalid);
	} else
		$('#respect-privacy').html('<i class="fa fa-smile-o"></i>' + laravel.mailAddressValid);
}

// Taken from http://stackoverflow.com/questions/12479897/detect-browser-file-input-support
function hasFileUploadSupport() {
	var hasSupport = true;
	try {
		var testFileInput = document.createElement('input');
		testFileInput.type = 'file';
		testFileInput.style.display = 'none';
		document.getElementsByTagName('body')[0].appendChild(testFileInput);
		if (testFileInput.disabled) {
			hasSupport = false;
		}
	} catch (ex) {
		hasSupport = false;
	} finally {
		if (testFileInput) {
			testFileInput.parentNode.removeChild(testFileInput);
		}
	}
	return hasSupport;
}

function doneTypingLoginSignup() {
	if (!timeoutLoginSignup) {
		return;
	}
	timeoutLoginSignup = null;

	$("#login-awesome span").text($('input#login-signup').val() + "? ");
	$("#login-awesome").fadeIn(500);
}

function doneTypingLoginPassword() {
	if (!timeoutPassword) {
		return;
	}
	timeoutPassword = null;

	$("#submit-form").removeClass("animated fadeInUp").addClass("animated shake");
}