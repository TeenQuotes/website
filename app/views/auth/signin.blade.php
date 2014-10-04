@extends('layouts.page')
@section('content')

	<div id="signin-page" class="row">
		<!-- Additional div to say that it is important to have an
		account before being able to submit a quote -->
		@if ($requireLoggedInAddQuote)
			@include('auth.loggedInAddQuote')
			@include('auth.signupCall')
			@include('auth.signinForm')
		
		<!-- Classic layout -->
		@else
			@include('auth.signinForm')
			@include('auth.signupCall')
		@endif

	</div>
@stop

@if ($requireLoggedInAddQuote)
	<!-- Send event to Google Analytics -->
	@section('add-js')
		@include('js.sendEvent')
	@stop
@endif