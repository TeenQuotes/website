@extends('layouts.page')

@section('content')
	<div id="contact-page">
		<h1 class="animated fadeInDown">{{ $title }}</h1>
		
		<div id="first-col" class="col-sm-6 animated fadeInUp">		
			<div id="stay-in-touch">
				<h2>{{ $stayInTouchTitle }}</h2>
				{{ $stayInTouchContent }}
			</div>
		</div>
		
		<div id="second-col" class="col-sm-6 animated fadeInUp">		
			<div id="contact-info">
				<h2>{{ $chooseYourWeapon }}</h2>
				<ul>
					<li>
						<i class="fa fa-envelope-o"></i> {{ HTML::mailto($emailAddress) }}
					</li>
					<li>
						<i class="fa fa-twitter"></i> {{ HTML::link('https://twitter.com/'.str_replace('@', '', $twitterAccount), $twitterAccount) }}
					</li>
				</ul>
			</div>
		</div>
	</div>
@stop