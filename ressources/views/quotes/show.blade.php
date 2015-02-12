@extends('layouts.page')

<?php $i = $quote->id % count($colors); ?>

@section('content')
	<!-- THE QUOTE -->
	@include('quotes.partials.singleQuote', compact("quote"))

	<!-- Favorites' Info-->
	@include('quotes.partials.favoritesInfo', ['data' => $quote->present()->favoritesData])

	<!-- Tags -->
	@include('quotes.partials.tags', ['tagsName' => $tagsName])

	<!-- SHOW COMMENTS -->
	@if ($quote->has_comments)
		<div class="animated fadeInUp">
			<h2 id="title-comments"><i class="fa fa-comments"></i>{{ Lang::get('comments.comments') }}</h2>
			@foreach ($quote->comments as $comment)
				@include('comments.singleComment', compact("comment"))
			@endforeach
		</div>
	@endif

	<!-- ADD A COMMENT -->
	<h2 id="title-add-comment"><i class="fa fa-pencil-square-o"></i>{{ Lang::get('comments.addComment')}}</h2>
	<!-- Banner no comments yet -->
	@if ( ! $quote->has_comments)
		<div class="alert alert-info no-hide">
			{{ Lang::get('comments.noCommentsYet')}}
		</div>
	@endif

	<!-- Form to add a comment -->
	@if (Auth::check())
		@include('comments.addComment', compact("quote"))
	<!-- Call to sign in / sign up -->
	@else
		@include('quotes.partials.signInCall')
	@endif
@stop

@section('social-networks-cards')
	@include('layouts.twitterCard')
	@include('layouts.facebookOpenGraph')
@stop