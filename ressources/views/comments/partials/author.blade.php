<?php $openedLink = false; ?>

@if ( ! isset($viewingSelfProfile) OR (isset($viewingSelfProfile) AND ! $viewingSelfProfile))
	<div class="col-xs-5 col-sm-5 col-md-7 col-lg-9">
		@if ( ! $comment->user->isHiddenProfile())
			<a href="{{ URL::route('users.show', ['id' => $comment->user->login]) }}" class="link-author-name">
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