<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	@include('emails.layout.html.style')
</head>
<body>
	<div id="background">
		<div id="container">
			<div id="logo-container">
				<a href="http://{{ Config::get('app.domain') }}" title="{{ Lang::get('layout.nameWebsite') }}"></a>
			</div>

			<div id="body-content">