function favorite(id_quote,id_user) {
	$(".favorite[data-id="+id_quote+"]").html("<em>Wait...</em>");
	$.ajax({
		type: 'post',
		url: 'http://teen-quotes.com/ajax/favorite.php',
		data: {
			id_quote: id_quote,
			id_user: id_user
		},
		success: function(data) {
			$(".favorite[data-id="+id_quote+"]").hide().html(data).fadeIn("slow");
		}
	});
	
	return false;
}

function unfavorite(id_quote,id_user) {
	$(".favorite[data-id="+id_quote+"]").html("<em>Wait...</em>");
	$.ajax({
		type: 'post',
		url: 'http://teen-quotes.com/ajax/unfavorite.php',
		data: {
			id_quote: id_quote,
			id_user: id_user
		},
		success: function(data) {
			$(".favorite[data-id="+id_quote+"]").hide().html(data).fadeIn("slow");
		}
	});
	
	return false;
}

function admin_quote(approve,id_quote,id_user) {
	$(".admin_quote[data-id="+id_quote+"]").html("<em>Wait...</em>");
	$.ajax({
		type: 'post',
		url: 'http://teen-quotes.com/ajax/admin_quote.php',
		data: {
			id_quote: id_quote,
			id_user: id_user,
			approve: approve
		},
		success: function(data) {
			$(".admin_quote[data-id="+id_quote+"]").html(data);
			$(".grey_post[data-id="+id_quote+"]").delay(500).slideUp(500);
		}
	});
	
	return false;
}

$(document).ready(function(){
	$(".follow").fadeIn(1500);
});