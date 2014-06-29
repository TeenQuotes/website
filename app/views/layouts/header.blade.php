<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ isset($pageTitle) ? $pageTitle : Lang::get('layout.nameWebsite') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{isset($pageDescription) ? $pageDescription : ''}}">
	{{ HTML::style('assets/css/styles.min.css') }}
	<link rel="shortcut icon" href="/assets/images/favicon.png"/>

	<!-- Special cards for social networks -->
	@yield('social-networks-cards')
	
	<!-- Deep links  -->
	@if (isset($deepLinksArray) AND !empty($deepLinksArray))
		@include('layouts.deepLinks')
	@endif

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<!-- Google Analytics -->
	@if (App::environment('production'))
		@include('layouts.analytics')
	@endif

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

				<div class="navbar-collapse collapse" id="navbar-main">
					<ul class="nav navbar-nav">
						<!-- LOGIN -->
						@if (Auth::guest())
							<li>
								<a href="{{URL::route('signin')}}"><i class="fa fa-user"></i> {{ Lang::get('layout.login') }}</a>
							</li>
						<!-- MY PROFILE -->
						@else
							<li>
								<a href="{{URL::route('users.show', Auth::user()->login)}}"><i class="fa fa-user"></i> {{ Lang::get('layout.myProfile') }}</a>
							</li>
						@endif

						<!-- RANDOM QUOTES -->
						<li>
							<a href="{{URL::route('random')}}"><i class="fa fa-random"></i>{{ Lang::get('layout.randomQuotes') }}</a>
						</li>

						<!-- ADD QUOTE -->
						<li>
							<a href="{{URL::route('addquote')}}"><i class="fa fa-comment"></i>{{ Lang::get('layout.addQuote') }}</a>
						</li>

						<!-- SEARCH -->
						<li>
							<a href="{{ URL::route('search.form')}}"><i class="fa fa-search"></i>{{ Lang::get('layout.search') }}</a>
						</li>

						<!-- APPS -->
						<li class="hidden-sm">
							<a href="{{ URL::route('apps')}}"><i class="fa fa-mobile"></i>{{ Lang::get('layout.apps') }}</a>
						</li>
					</ul>

					<!-- TWITTER FOLLOW BUTTON -->
					<ul class="nav navbar-nav navbar-right hidden-sm hidden-xs hidden-md">
						<li>
							<a href="http://twitter.com/ohteenquotes" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @ohteenquotes</a>
						</li>
					</ul>
				</div>
			</div>
		</div><!-- END NAVBAR -->