<?php
$animation = isset($fadeLeft) ? " animated fadeInLeft" : '';
?>

<div data-id="{{ $comment->id }}" class="comment{{ $animation }}">
	<!-- CONTENT AND AVATAR -->
	<div class="row">
		<!-- Avatar -->
		@include('comments.partials.avatar')

		<!-- Content -->
		<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
			{{{ $comment->content }}}
		</div>
	</div>

	<!-- COMMENT'S INFO -->
	<div class="row comment-info">
		
		<!-- Delete and edit my comment on mobile -->
		<div class="col-xs-3 col-sm-3 col-md-2 col-lg-1">
			<div class="text-center hidden-sm hidden-md hidden-lg">
				@include('comments.partials.controls')
			</div>
		</div>

		<!-- Date -->
		@if (isset($viewingSelfProfile) AND $viewingSelfProfile)
			<div class="date-comment col-xs-9 col-sm-9 col-md-10 col-lg-11">
		@else
			<div class="date-comment col-xs-4 col-sm-4 col-md-3 col-lg-2">
		@endif
			
			<!-- Delete and edit my comment on large devices -->
			<div class="hidden-xs controls-large">
				@include('comments.partials.controls')
			</div>

			{{{ $comment->present()->commentAge }}}
		</div>
		
		<!-- Author name -->
		@include('comments.partials.author')
	</div>
</div>