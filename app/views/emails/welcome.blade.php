@extends('emails/default')

@section('content')
	{{ Lang::get('auth.welcomeEmailWithUsername', array('login' => $login)) }}
	<br/><br/>
	{{ Lang::get('auth.bodyWelcomeEmail', array('login' => $login, 'linkEditProfile' => URL::route('users.edit', array($login)))) }}
@stop