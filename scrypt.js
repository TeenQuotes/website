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
			$(".favorite[data-id="+id_quote+"]").hide().html("<span class=\"hide_this\" data-id="+id_quote+">"+data+"</span><span class=\"show_this\" data-id="+id_quote+"><a href=\"\"  onclick=\"unfavorite("+id_quote+","+id_user+"); return false;\" title=\"Delete this quote from your favorites\"><img src=\"http://www.teen-quotes.com/images/icones/broken_heart.gif\" /></a></span>").fadeIn(1000);
			$(".show_this[data-id="+id_quote+"]").hide().delay(3000).fadeIn(1000);
			$(".hide_this[data-id="+id_quote+"]").delay(2000).fadeOut(1000);
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
			$(".favorite[data-id="+id_quote+"]").hide().html("<span class=\"hide_this\" data-id="+id_quote+">"+data+"</span><span class=\"show_this\" data-id="+id_quote+"><a href=\"\"  onclick=\"favorite("+id_quote+","+id_user+"); return false;\" title=\"Add this quote to your favorites !\"><img src=\"http://www.teen-quotes.com/images/icones/heart.png\" /></a></span>").fadeIn(1000);
			$(".show_this[data-id="+id_quote+"]").hide().delay(3000).fadeIn(1000);
			$(".hide_this[data-id="+id_quote+"]").delay(2000).fadeOut(1000);
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