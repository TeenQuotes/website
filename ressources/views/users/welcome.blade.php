@extends('users.show')

@section('welcome-tutorial')
	<div id="welcome-profile">
		{{ $welcomeText }}

		{{-- Suggest to write a tweet --}}
		@include('users.partials.getting-started')

		{{-- Some welcome cards, to suggest some actions --}}
		@include('users.partials.welcome-cards')

	</div>
@stop
