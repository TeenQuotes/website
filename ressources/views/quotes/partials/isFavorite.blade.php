<div class="col-md-3 col-sm-2 col-xs-2 favorite-links animated fadeIn">
	<!-- LOGGED IN USER -->
	@if (Auth::check())
		@if ($quote->isFavoriteForCurrentUser())
			<button data-url="{{URL::route('unfavorite', array($quote->id), true)}}" data-id="{{ $quote->id }}" data-type="unfavorite" class="badge favorite-action"><i class="fa fa-heart"></i><span class="count">{{ $quote->totalFavorites }}</span></button>
		@else
			<button data-url="{{URL::route('favorite', array($quote->id), true)}}" data-id="{{ $quote->id }}" data-type="favorite" class="badge favorite-action"><i class="fa fa-heart-o"></i><span class="count">{{ $quote->totalFavorites }}</span></button>
		@endif
	<!-- GUEST -->
	@else
		<button class="badge fav-count"><i class="fa fa-heart"></i><span class="count">{{ $quote->totalFavorites }}</span></button>
	@endif
</div>
