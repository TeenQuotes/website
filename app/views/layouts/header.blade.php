<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ isset($pageTitle) ? $pageTitle : Lang::get('layout.nameWebsite') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{isset($pageDescription) ? $pageDescription : ''}}">
	{{ HTML::style('//netdna.bootstrapcdn.com/bootswatch/3.1.1/cosmo/bootstrap.min.css'); }}
	{{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'); }}
	{{ HTML::style('assets/css/screen.css'); }}
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>