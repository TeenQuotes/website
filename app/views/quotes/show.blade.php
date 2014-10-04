@extends('layouts.page')
<?php
$i = $quote->id % count($colors);
?>

@section('content')
	<!-- THE QUOTE -->
	@include('quotes.singleQuote', compact("quote"))


	<!-- Favorites' Info-->
	@include('quotes.favoritesInfo', ['data' => $quote->present()->favoritesData])

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
		{{ Lang::get('auth.mustBeLoggedToAddcooment') }}
		<div class="row" id="require-log-buttons">
			<div class="text-center col-sm-6">
				<a href="{{URL::route('signin')}}" class="transition btn btn-warning btn-lg">{{Lang::get('auth.iHaveAnAccount')}}</a>
			</div>
			<div class="text-center col-sm-6">
				<a href="{{URL::route('signup')}}" class="transition btn btn-success btn-lg">{{Lang::get('auth.wantsAnAccount')}}</a>
			</div>
		</div>
	@endif
@stop

@section('social-networks-cards')
	@include('layouts.twitterCard')
	@include('layouts.facebookOpenGraph')
@stop