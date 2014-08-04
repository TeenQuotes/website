<!-- This view is extended by users.welcome -->
@extends('layouts/page')

@section('content')
	<div id="user-profile">
		<!-- Alert if the profile is hidden -->
		@if ($user->isHiddenProfile() AND $viewingSelfProfile)
			<div class="alert alert-info alert-dismissable no-hide">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info"></i> {{ Lang::get('users.profileCurrentlyHidden') }}
			</div>
		@endif
		
		<!-- Profile basic info and counters -->
		@include('users.profile.info')

		<!-- Controls buttons -->
		@if ($viewingSelfProfile)
			@include('users.profile.controls')
		@endif

		<!-- Content: quotes or comments -->
		@if (!empty($quotes))
			@if ($type == 'favorites')
				<h2><i class="fa fa-heart"></i> {{ Lang::get('users.favoriteQuotes') }}</h2>
			@elseif ($type == 'comments')	
				<h2><i class="fa fa-comments"></i> {{ Lang::get('users.comments') }}</h2>
			@else
				<h2><i class="fa fa-pencil"></i> {{ Lang::get('users.publishedQuotes') }}</h2>
			@endif

			<?php $i = 0; ?>
			@foreach ($quotes as $quote)
				<!-- Quotes -->
				@if ($type != 'comments')
					@include('quotes.singleQuote', compact("quote"))
				<!-- Comments -->
				@else
					<?php $comment = $quote; ?>
					@include('comments.singleComment', ["comment" => $comment, "fadeLeft" => true])
					<div data-comment-id="{{ $comment->id }}" class="comment-quote-info animated fadeInRight">
						<a href="{{URL::route('quotes.show', $comment->quote->id)}}">#{{$comment->quote->id}}</a> - {{{ $comment->quote->content }}}
					</div>
				@endif
			<?php $i++ ?>
			@endforeach

			<div class="text-center">
				{{ $paginator->links() }}
			</div>
		@endif
		
		<!-- The welcome tutorial, if available -->
		@yield("welcome-tutorial")
	</div>
@stop