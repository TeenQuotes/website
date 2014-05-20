<?php $openedLink = false; ?>
<div class="comment">
	<!-- COMMENT AND AVATAR -->
	<div class="row">
		<!-- Avatar -->
		<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
			<a href="{{ URL::action('UsersController@show', ['id' => $comment->user->login]) }}"><img class="avatar img-responsive" src="{{{ $comment->user->getURLAvatar() }}}"/></a>
		</div>
		<!-- Content -->
		<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
			{{{ $comment->content }}}
		</div>
	</div>

	<!-- COMMENT'S INFO -->
	<div class="row comment-info">
		<!-- Date -->
		<div class="date-comment col-xs-offset-3 col-sm-offset-3 col-md-offset-2 col-lg-offset-1 col-xs-4 col-sm-4 col-md-3 col-lg-2">
			{{{ $comment->created_at->diffForHumans() }}}
		</div>
		<!-- Author name -->
		<div class="col-xs-5 col-sm-5 col-md-7 col-lg-9">
			@if (!$comment->user->isHiddenProfile())
				<a href="{{ URL::action('UsersController@show', ['id' => $comment->user->login]) }}" class="transition link-author-name">
				<?php $openedLink = true; ?>
			@else
				<span class="link-author-name">
			@endif

			{{{ $comment->user->login }}}

			@if ($openedLink)
				</a>
			@else
				</span>
			@endif
		</div>
	</div>
</div>