<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
	@if ( ! $comment->user->isHiddenProfile())
		<a href="{{ URL::route('users.show', $comment->user->login) }}">
	@endif
		<img class="avatar img-responsive" src="{{{ $comment->user->present()->avatarLink }}}"/>
	@if ( ! $comment->user->isHiddenProfile())
		</a>
	@endif
</div>