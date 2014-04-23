@extends('layouts/page')
<?php
$i = rand(0, count($colors) - 1);
?>

@section('content')
	@include('layouts.singleQuote', array(compact($quote)))

	@foreach ($comments as $comment)
		@include('comments.singleComment', array(compact($comment)))
	@endforeach
@stop