<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ isset($pageTitle) ? $pageTitle : Lang::get('layout.nameWebsite') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	@if (isset($pageDescription))
		<meta name="description" content="{{ $pageDescription }}">
	@endif
	{{ HTML::style('assets/css/styles.min.css') }}
	<link rel="shortcut icon" href="/assets/images/favicon.png"/>

	<!-- Special cards for social networks -->
	@yield('social-networks-cards')

	<!-- Deep links  -->
	@if (isset($deepLinksArray) AND ! empty($deepLinksArray))
		@include('layouts.deepLinks')
	@endif

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!-- Color of the address bar in Chrome -->
	<meta name="theme-color" content="#22313F">
</head>
<body>

	<!-- Google Analytics -->
	@include('layouts.analytics')

	<div id="wrap">
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<a href="{{ URL::route('home') }}" class="logo"></a>

					<!-- NAVBAR TOGGLE FOR MOBILES -->
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

				<!-- MENU -->
				@include('layouts.partials.menu')

			</div>
		</div><!-- END NAVBAR -->
