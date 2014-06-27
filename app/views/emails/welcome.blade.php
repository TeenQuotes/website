@extends('emails/default')
<?php
$urlEditProfile = URL::route('users.edit', array($login));
$urlCampaignEditProfile = TextTools::linkCampaign($urlEditProfile, 'callToEditProfile', 'email', 'welcome', 'linkBodyEmail');

$urlProfile = URL::route('users.show', array($login));
$urlCampaignProfile = TextTools::linkCampaign($urlProfile, 'callToProfile', 'email', 'welcome', 'linkBodyEmail');

$data = [
	'login'                  => $login,
	'urlCampaignEditProfile' => $urlCampaignEditProfile,
	'urlEditProfile'         => $urlEditProfile,
	'urlCampaignProfile'     => $urlCampaignProfile,
	'urlProfile'             => $urlProfile,
];
?>

@section('content')
	{{ Lang::get('auth.welcomeEmailWithUsername', array('login' => $login)) }}
	<br/><br/>
	{{ Lang::get('auth.bodyWelcomeEmail', $data) }}
@stop