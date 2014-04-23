@extends('layouts/page')
<?php
$i = rand(0, count($colors) - 1);
?>

@section('content')
	@include('quotes.singleQuote', compact($quote))

	<h2 id="title-comments"><i class="fa fa-comments"></i><span class="green">{{ Lang::get('comments.comments') }}</span></h2>
	@foreach ($comments as $comment)
		@include('comments.singleComment', compact($comment))
	@endforeach
	
	@if (Auth::check())
		@include('comments.addComment', compact($quote))
	@endif
@stop