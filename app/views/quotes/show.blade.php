@extends('layouts/page')
<?php
$i = rand(0, count($colors) - 1);
?>

@section('content')
	@include('quotes.singleQuote', compact($quote))

	<h2 id="title-comments"><i class="fa fa-comments"></i><span class="green">{{ Lang::get('comments.comments') }}</span></h2>
		@if (!$quote->has_comments)
			<div class="alert alert-info no-hide">
				{{ Lang::get('comments.noCommentsYet')}}
			</div>
		@endif
	@foreach ($comments as $comment)
		@include('comments.singleComment', compact($comment))
	@endforeach

	@if (Auth::check())
		@include('comments.addComment', compact($quote))
	@else
		<h2 id="title-add-comment"><i class="fa fa-pencil-square-o"></i><span class="red">{{ Lang::get('comments.addComment')}}</span></h2>

		{{ Lang::get('auth.mustBeLoggedToAddcooment') }}
		<div class="row" id="require-log-buttons">
			<div class="text-center col-xs-6">
				<a href="{{URL::route('signin')}}" class="transition btn btn-warning btn-lg">{{Lang::get('auth.iHaveAnAccount')}}</a>
			</div>
			<div class="text-center col-xs-6">
				<a href="{{URL::route('signup')}}" class="transition btn btn-success btn-lg">{{Lang::get('auth.wantsAnAccount')}}</a>
			</div>
		</div>
	@endif
@stop