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
			$(".favorite[data-id="+id_quote+"]").html(data);
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
			$(".favorite[data-id="+id_quote+"]").html(data);
		}
	});
	
	return false;
}

$(document).ready(function(){
	$(".follow").fadeIn(1500);
});
