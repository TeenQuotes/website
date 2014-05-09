@extends('emails/default')
<?php
$linkEditProfile = URL::route('users.edit', array($login));
$linkCampaignText = TextTools::linkCampaign($linkEditProfile, 'callToEditProfile', 'email', 'welcome', 'linkBodyEmail');
?>

@section('content')
	{{ Lang::get('auth.welcomeEmailWithUsername', array('login' => $login)) }}
	<br/><br/>
	{{ Lang::get('auth.bodyWelcomeEmail', array('login' => $login, 'linkEditProfile' => $linkCampaignText, 'url' => $linkEditProfile)) }}
@stop