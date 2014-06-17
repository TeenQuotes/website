<div class="row stats-counter">
	@foreach (['quotesPublishedCount', 'favCount', 'commentsCount', 'addedFavCount'] as $element)
		<?php $openedLink = false; ?>
		@if ($$element > 0)
			<div class="col-xs-6 col-md-3 text-center">
				<!-- LINKS  -->
				@if ($type != 'favorites' AND $element == 'favCount')
					<a href="{{ URL::route('users.show', [$user->login, 'favorites']) }}">
					<?php $openedLink = true; ?>
				@endif

				@if ($type != 'comments' AND $element == 'commentsCount')
					<a href="{{ URL::route('users.show', [$user->login, 'comments']) }}">
					<?php $openedLink = true; ?>
				@endif

				@if ($type != 'published' AND $element == 'quotesPublishedCount')
					<a href="{{ URL::route('users.show', [$user->login]) }}">
					<?php $openedLink = true; ?>
				@endif

				<span class="description">{{ Lang::choice('users.'.$element.'Text', $$element) }}</span>
				
				<!-- COUNTER  -->
				<!-- Add an ID to the counter to increase / decrease on the user's profile -->
				@if (Auth::check() AND Auth::user()->login == $user->login)
					<span class="counter" id="{{ Str::snake($element, '-') }}">{{ $$element }}</span>
				@else
					<span class="counter">{{ $$element }}</span>
				@endif

				@if ($openedLink)
					</a>
				@endif

				<!-- ACTIVE OR NOT -->
				@if (($type == 'comments' AND $element == 'commentsCount') OR ($type == 'published' AND $element == 'quotesPublishedCount') OR ($type == 'favorites' AND $element == 'favCount'))
					<div class="active"></div>
				@else
					<div class="unactive"></div>
				@endif
			</div>
		@endif
	@endforeach
</div>