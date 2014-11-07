@extends('emails.default')

@section('content')
	{{ Lang::get('auth.welcomeEmailWithUsername', compact('login')) }}
	<br/><br/>
	{{-- The data array is created from a view composer --}}
	{{ Lang::get('auth.bodyWelcomeEmail', $data) }}
@stop