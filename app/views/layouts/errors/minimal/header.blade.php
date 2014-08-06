<!DOCTYPE html>
<html>
<head>
	<title>{{ $pageTitle or 'Oops, something is wrong!'}}</title>
	<link href='http://fonts.googleapis.com/css?family=PT+Mono' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width">

	<!-- AUTO REFRESH 10s -->
	<meta http-equiv="refresh" content="10; URL={{ URL::route('home') }}">

	<style>
		* { margin: 0; padding: 0; }
		html, body {height: 100%;background: #313131;color: #FFF;font-family:'PT Mono', Arial;}
		.container {position: absolute;width: 100%;height: 100%;display: table;}
		span {height: 100%;vertical-align: middle;display: inline-block; }
		.middle {vertical-align: middle;display: table-cell;}

    	h1 {font-size: 2.5em; text-align: center; margin-bottom:50px;}
    	p {margin: 0 auto 20px auto;padding: 0;font-size: 2em;max-width: 80%;line-height: 1.5}
    	a {color: #CCC;}

		a#logo {		
	    	background-image: url("/assets/images/logo.png");
			background-repeat: no-repeat;
			width: 200px;
			height: 30px;
			display: block;
			margin: 50px 10%;
			float: left;
		}

    	@media only screen and (max-width: 767px) {
			h1 {font-size: 2em;}
			p {font-size: 1.5em;}
		}
		@media only screen and (max-width: 400px) {
			h1 {font-size: 1.8em;}
			p {font-size: 1.4em;}
		}
	</style>
</head>

<body>
	<a id="logo" href="{{ URL::route('home') }}"></a>
	<div class="container">
	    <div class="middle">