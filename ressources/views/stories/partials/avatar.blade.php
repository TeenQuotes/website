<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
	@if ( ! $story->user->isHiddenProfile())
		<a href="{{ URL::route('users.show', ['id' => $story->user->login]) }}">
	@endif
		<img class="avatar img-responsive" src="{{{ $story->user->present()->avatarLink }}}"/>
	@if ( ! $story->user->isHiddenProfile())
		</a>
	@endif
</div>