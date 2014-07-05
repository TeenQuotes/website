@extends('emails/default')
<?php
$urlProfile = URL::route('users.show', array($login));
$urlCampaignProfile = TextTools::linkCampaign($urlProfile, 'callToProfile', 'email', 'welcome', 'linkBodyEmail');

$data = [
	'login'              => $login,
	'urlCampaignProfile' => $urlCampaignProfile,
	'urlProfile'         => $urlProfile,
];
?>

@section('content')
	{{ Lang::get('auth.welcomeEmailWithUsername', array('login' => $login)) }}
	<br/><br/>
	{{ Lang::get('auth.bodyWelcomeEmail', $data) }}
@stop