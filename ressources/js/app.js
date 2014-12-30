window.laravel = window.laravel || {};
var timeoutLoginSignup, timeoutPassword;

$('html, body').hide();

$(document).ready(function() {
	new WOW().init();

	$('.social-buttons a i').delay(200).queue(function() {
		$(this).addClass('animated fadeIn');
	});

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

	// Signup promotion when indexing quotes
	$("#js-promotion-signup").hover(function() {
		ga('send', 'event', 'signup', 'promotion-quote-signup', 'click');
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
		var msg;
		if (nbCaracters < 50)
			msg = '<i class="fa fa-meh-o"></i> ' + laravel.contentShortHint;
		else
			msg = '<i class="fa fa-smile-o"></i> ' + laravel.contentGreatHint;

		$('#countLetters').html(msg);
		if (nbCaracters >= 50) {
			$('#countLetters').addClass("green");
			$('#countLetters').removeClass("orange");
			$('input[type="submit"]').removeAttr('disabled');
		} else {
			$('input[type="submit"]').attr('disabled', 'disabled');
			$('#countLetters').addClass("orange");
			$('#countLetters').removeClass("green");
		}

		if ($('#countLetters').is(":visible") && nbCaracters === 0) {
			$('#countLetters').fadeOut("slow");
		}
		if ($('#countLetters').is(":hidden") && nbCaracters >= 1) {
			$('#countLetters').fadeIn("slow");
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
		var msg;
		if (nbCaracters < 10)
			msg = '<i class="fa fa-meh-o"></i> ' + laravel.contentShortHint;
		else
			msg = '<i class="fa fa-smile-o"></i> ' + laravel.contentGreatHint;

		$('#countLetters').html(msg);
		if (nbCaracters >= 10) {
			$('#countLetters').addClass("green");
			$('#countLetters').removeClass("orange");
			$('input[type="submit"]').removeAttr('disabled');
		} else {
			$('input[type="submit"]').attr('disabled', 'disabled');
			$('#countLetters').addClass("orange");
			$('#countLetters').removeClass("green");
		}

		if ($('#countLetters').is(":visible") && nbCaracters === 0) {
			$('#countLetters').fadeOut("slow");
		}
		if ($('#countLetters').is(":hidden") && nbCaracters >= 1) {
			$('#countLetters').fadeIn("slow");
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
	if ($("#editprofile-page").length && !hasFileUploadSupport())
		$("#change-avatar").hide();
	else
		$("#alert-change-avatar").hide();

	// Favorite / Unfavorite
	$('button.favorite-action').click(function() {
		var otherType, otherIcon, url, type, id_quote, iconValidation, nbFav;
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
				if (data.success === false) {
					var arr = data.errors;
					$.each(arr, function(index, value) {
						if (value.length !== 0) {
							$("#validation-errors").append('<div class="alert alert-error"><strong>' + value + '</strong><div>');
						}
					});
					// Success
				} else {
					if (type == "favorite") {
						otherType = "unfavorite";
						otherIcon = "fa-heart";
					} else {
						otherType = "favorite";
						otherIcon = "fa-heart-o";
					}

					// Update counter on the user's profile
					if ($("#fav-count").length) {
						nbFav = parseInt($("#fav-count").text(), 10);
						if (type == 'favorite')
							$("#fav-count").text(nbFav + 1);
						else {
							$("#fav-count").text(nbFav - 1);
							var animation;

							// Hide the quote on the profile
							if ($('.quote[data-id=' + id_quote + ']').hasClass('fadeInLeft'))
								animation = 'fadeOutLeft';
							else
								animation = 'fadeOutRight';
							$('.quote[data-id=' + id_quote + ']').removeClass(animation.replace('Out', 'In')).addClass(animation);

							// Remove from DOM after 1s
							setTimeout(function() {
								$('.quote[data-id=' + id_quote + ']').remove();
							}, 1000);
						}
					}

					// Update counter on single quote
					if ($("button[data-id=" + id_quote + "] .count").length) {
						nbFav = parseInt($("button[data-id=" + id_quote + "] .count").text(), 10);
						if (type == 'favorite')
							nbFav++;
						else
							nbFav--;
					}

					$(".favorite-action[data-id=" + id_quote + "]").attr('data-url', url.replace(type, otherType));
					$(".favorite-action[data-id=" + id_quote + "]").attr('data-type', otherType);
					$(".favorite-action[data-id=" + id_quote + "]").html("<i class='fa animated fadeIn " + otherIcon + "'></i><span class='count'>" + nbFav + "</span>");

					// If we are viewing a single quote, update users who favorited this quote
					if (typeof laravel.urlFavoritesInfo !== 'undefined') {
						$.ajax({
							type: 'post',
							cache: false,
							crossDomain: true,
							url: laravel.urlFavoritesInfo,
							dataType: 'json',
							data: {
								'id': id_quote
							},
							success: function(data) {
								var translate = data.translate;
								$('.favorites-info .text').fadeOut(function() {
									$(this).html(translate).fadeIn();
								});
							},
							error: function(xhr, textStatus, thrownError) {
								console.log(xhr);
								console.log(textStatus);
								console.log(thrownError);
								console.log(xhr.responseText);
								alert('Something went to wrong. Please try again later.');
							}
						});
					}
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

	// Delete a comment
	$('i.delete-comment').click(function() {
		var url, id_comment;
		url = $(this).attr('data-url');
		id_comment = $(this).attr('data-id');

		$.ajax({
			type: 'DELETE',
			cache: false,
			url: $(this).attr('data-url'),
			dataType: 'json',
			data: {},
			success: function(data) {
				// Success
				if (data.success) {
					// Update counter on the user's profile
					if ($("#comments-count").length) {
						var nbComments = parseInt($("#comments-count").text(), 10);
						$("#comments-count").text(nbComments - 1);
					}

					// Hide the comment with a CSS fading out effect
					$(".comment[data-id=" + id_comment + "]").removeClass('animated').addClass('animated fadeOutLeft');
					$(".comment-quote-info[data-comment-id=" + id_comment + "]").removeClass('animated').addClass('animated fadeOutRight');

					// Remove from DOM after 1s
					setTimeout(function() {
						$(".comment[data-id=" + id_comment + "]").remove();
						$(".comment-quote-info[data-comment-id=" + id_comment + "]").remove();
					}, 1000);
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

	// Force login verification at signing up for smartphones
	$("#email-signup").focusin(function() {
		if ($("#login-signup").val().length > 0 && $("#login-validator").is(':empty')) {
			timeoutLoginSignup = 42;
			doneTypingLoginSignup();
		}
	});

	// Smooth scroll
	$('a[href^=#]').bind("click", jump);

	if (location.hash) {
		setTimeout(function() {
			$('html, body').scrollTop(0).show();
			jump();
		}, 0);
	} else {
		$('html, body').show();
	}

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
				// Errors
				if (data.success === false) {
					var arr = data.errors;
					$.each(arr, function(index, value) {
						if (value.length !== 0) {
							$("#validation-errors").append('<div class="alert alert-error"><strong>' + value + '</strong><div>');
						}
					});
					// Success
				} else {
					// Update nb quotes awaiting moderation
					numberOfQuoteWaitingModeration = parseInt($("#nb-quotes-waiting").text(), 10) - 1;
					$("#nb-quotes-waiting").text(numberOfQuoteWaitingModeration);

					// Update nb quotes pending
					if (decision == 'approve') {

						numberOfQuotePending = parseInt($("#nb-quotes-pending").text(), 10) + 1;
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

	$("#email-error").remove();
	if (!isValid) {
		if (alternate) {
			$('#respect-privacy').html(laravel.didYouMean + "<span id='alternate-email'>" + alternate + "</span>?");
			ga('send', 'event', 'signup', 'fill-email', 'suggested-email');
		} else {
			$('#respect-privacy').html('<i class="fa fa-meh-o"></i>' + laravel.mailAddressInvalid);
			ga('send', 'event', 'signup', 'fill-email', 'wrong-email');
		}
	} else {
		$('#respect-privacy').html('<i class="fa fa-smile-o"></i>' + laravel.mailAddressValid);
		ga('send', 'event', 'signup', 'fill-email', 'valid-email');
	}
}

// Taken from http://stackoverflow.com/questions/12479897/detect-browser-file-input-support
function hasFileUploadSupport() {
	var hasSupport = true;
	var testFileInput;
	try {
		testFileInput = document.createElement('input');
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

	ga('send', 'event', 'support-file-upload', 'browser-has-feature', hasSupport);

	return hasSupport;
}

var jump = function(e) {
	var target;
	if (e) {
		e.preventDefault();
		target = $(this).attr("href");
	} else {
		target = location.hash;
	}

	$('html,body').animate({
		scrollTop: $(target).offset().top - 80
	}, 2000, function() {
		location.hash = target;
	});
};

function doneTypingLoginSignup() {
	if (!timeoutLoginSignup) {
		return;
	}
	timeoutLoginSignup = null;

	var icon = "fa fa-thumbs-up";
	$.ajax({
		type: 'post',
		cache: false,
		crossDomain: true,
		url: laravel.urlLoginValidator,
		dataType: 'json',
		data: {
			'login': $('input#login-signup').val()
		},
		success: function(data) {
			// Errors
			if (data.success === false) {
				icon = "fa fa-meh-o";
				$("#login-validator").html("<i class='" + icon + "'></i>" + data.message);
				$("#login-validator i.fa").removeClass("green").addClass("black");
				$("#login-error").remove();
				$("#login-validator").removeClass("green").addClass("orange").fadeIn(500);
				ga('send', 'event', 'signup', 'fill-login', {
					reason: 'wrong-login',
					rule: data.failed
				});
				// Success
			} else {
				$("#login-validator").html("<i class='" + icon + "'></i>" + data.message);
				$("#login-validator i.fa").removeClass("black").addClass("green");
				$("#login-error").remove();
				$("#login-validator").removeClass("orange").addClass("green").fadeIn(500);
				ga('send', 'event', 'signup', 'fill-login', 'right-login');
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
}

function doneTypingLoginPassword() {
	if (!timeoutPassword) {
		return;
	}
	timeoutPassword = null;

	$("#submit-form").removeClass("animated fadeInUp").addClass("animated shake");
}