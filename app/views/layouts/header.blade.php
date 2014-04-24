<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ isset($pageTitle) ? $pageTitle : Lang::get('layout.nameWebsite') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{isset($pageDescription) ? $pageDescription : ''}}">
	{{ HTML::style('//netdna.bootstrapcdn.com/bootswatch/3.1.1/cosmo/bootstrap.min.css'); }}
	{{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'); }}
	{{ HTML::style('//cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css'); }}
	{{ HTML::style('/assets/css/screen.css'); }}
	<link rel="shortcut icon" href="assets/images/favicon.png"/>

	<!-- TWITTER CARD FOR SINGLE QUOTE -->
	@if (Route::currentRouteName() == 'quotes.show')
		@include('layouts.twitterCard', compact($quote))
	@endif
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a href="../" class="logo"></a>

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
							<a class="transition" href="{{URL::route('signin')}}"><i class="fa fa-user"></i> {{ Lang::get('layout.login') }}</a>
						</li>
					@endif

					<!-- RANDOM QUOTES -->
					<li>
						<a class="transition" href="{{URL::route('random')}}"><i class="fa fa-random"></i>{{ Lang::get('layout.randomQuotes') }}</a>
					</li>

					<!-- ADD QUOTE -->
					<li>
						<a class="transition" href="{{URL::route('addquote')}}"><i class="fa fa-comment"></i>{{ Lang::get('layout.addQuote') }}</a>
					</li>

					<!-- APPS -->
					<li>
						<a class="transition" href="/"><i class="fa fa-mobile fa-lg"></i>{{ Lang::get('layout.apps') }}</a>
					</li>

					<!-- LOGOUT -->
					@if (Auth::check())
						<li>
							<a class="transition" href="{{URL::route('logout')}}"><i class="fa fa-sign-out"></i> {{ Lang::get('layout.logout') }}</a>
						</li>
					@endif
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