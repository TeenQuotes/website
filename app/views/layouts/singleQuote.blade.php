<div class="quote" style="background-color:{{$colors[$i]}};border-bottom-color:{{Quote::adjustBrightness($colors[$i], -20)}}">
	#{{ $quote->id}}
	{{ $quote->content}}
	
	<div class="row">
		<div class="col-md-1">
			@if ($quote->has_comments)
				<span class="badge">{{$quote->total_comments}}</span>
			@endif
		</div>
		<div class="col-md-1">
			@if ($quote->is_favorite_for_current_user)
				<span class="badge">favorite</span>
			@else
				<span class="badge">no favorite</span>
			@endif
		</div>
		<div class="col-md-6">
			.col-md-6
		</div>
		<div class="col-md-4">
			<a href="{{ URL::action('UsersController@show', ['id' => $quote->user->login]) }}">{{$quote->user->login}}</a>
		</div>
	</div>
</div>