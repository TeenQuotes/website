<?php $openedLink = false; ?>
<div class="row stats-counter">
	@foreach (['quotesPublishedCount', 'favCount', 'commentsCount', 'addedFavCount'] as $element)
		@if ($$element > 0)
			<div class="col-xs-6 col-md-3 text-center">
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
				<span class="counter">{{ $$element }}</span>

				@if ($openedLink)
					</a>
				@endif

				<!-- Active or not -->
				@if (($type == 'comments' AND $element == 'commentsCount') OR ($type == 'published' AND $element == 'quotesPublishedCount') OR ($type == 'favorites' AND $element == 'favCount'))
					<div class="active"></div>
				@else
					<div class="unactive"></div>
				@endif
			</div>
		@endif
	@endforeach
</div>