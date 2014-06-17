@extends('layouts/page')
@section('content')
	<div id="user-profile">
		@if ($user->isHiddenProfile() AND Auth::check() AND Auth::user()->login == $user->login)
			<div class="alert alert-info alert-dismissable no-hide">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info"></i> {{ Lang::get('users.profileCurrentlyHidden') }}
			</div>
		@endif
		@include('users.profile.info')

		@include('users.profile.controls')

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
				@if ($type != 'comments')
					@include('quotes.singleQuote', compact("quote"))
				@else
					<?php $comment = $quote; ?>
					@include('comments.singleComment', compact("comment"))
					<div class="comment-quote-info">
						<a href="{{URL::route('quotes.show', $comment->quote->id)}}">#{{$comment->quote->id}}</a> - {{$comment->quote->content}}
					</div>
				@endif
			<?php $i++ ?>
			@endforeach

			<div class="text-center">
				{{ $paginator->links() }}
			</div>
		@endif
	</div>
@stop