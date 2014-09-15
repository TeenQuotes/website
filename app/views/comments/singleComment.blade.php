<?php
$openedLink = false;
$animation = isset($fadeLeft) ? " animated fadeInLeft" : '';
?>

<div data-id="{{ $comment->id }}" class="comment{{ $animation }}">
	<!-- COMMENT AND AVATAR -->
	<div class="row">
		<!-- Avatar -->
		<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
			@if (!$comment->user->isHiddenProfile())
				<a href="{{ URL::action('UsersController@show', ['id' => $comment->user->login]) }}">
			@endif
				<img class="avatar img-responsive" src="{{{ $comment->user->getURLAvatar() }}}"/>
			@if (!$comment->user->isHiddenProfile())
				</a>
			@endif
		</div>
		<!-- Content -->
		<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
			{{{ $comment->content }}}
		</div>
	</div>

	<!-- COMMENT'S INFO -->
	<div class="row comment-info">
		<!-- Date -->
		@if (isset($viewingSelfProfile) AND $viewingSelfProfile)
			<div class="date-comment col-xs-offset-3 col-sm-offset-3 col-md-offset-2 col-lg-offset-1 col-xs-9 col-sm-9 col-md-10 col-lg-11">
		@else
			<div class="date-comment col-xs-offset-3 col-sm-offset-3 col-md-offset-2 col-lg-offset-1 col-xs-4 col-sm-4 col-md-3 col-lg-2">
		@endif
			@if ($comment->isPostedBySelf())
				<i class="delete-comment fa fa-times" data-id="{{{ $comment->id }}}" data-url="{{ URL::route('comments.destroy', $comment->id) }}"></i>
			@endif

			{{{ $comment->created_at->diffForHumans() }}}
		</div>
		<!-- Author name -->
		@if (!isset($viewingSelfProfile) OR (isset($viewingSelfProfile) AND !$viewingSelfProfile))
			<div class="col-xs-5 col-sm-5 col-md-7 col-lg-9">
				@if (!$comment->user->isHiddenProfile())
					<a href="{{ URL::action('UsersController@show', ['id' => $comment->user->login]) }}" class="link-author-name">
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
		@endif
	</div>
</div>