@extends('emails/default')
<?php
$carbon = Carbon::createFromFormat('Y-m-d', $user['birthdate']);
?>

@section('content')
	<img src="{{ Lang::get('email.imageBirthday') }}" id="img-event" />
	{{ Lang::get('email.hiWithLogin', array('login' => $user['login'])) }}
	<br/><br/>
	{{ Lang::get('email.birthdayContent', ['login' => $user['login'], 'age' => $carbon->age]) }}
@stop