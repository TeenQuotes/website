@extends('layouts.page')

@section('content')
	<div id="contact-page">
		<!-- CONTACT -->
		<h1 class="animated fadeInDown">{{ $contactTitle }}</h1>
		
		<div id="first-col" class="col-sm-6 animated fadeInDown">		
			<div id="stay-in-touch">
				<h2>{{ $stayInTouchTitle }}</h2>
				{{ $stayInTouchContent }}
			</div>
		</div>
		
		<div id="second-col" class="col-sm-6 animated fadeInDown">		
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

		<div class="clearfix"></div>

		<!-- TEAM LIST -->
		<h1 id="team-title" class="animated fadeInUp">{{ $teamTitle }}</h1>
		
		<div id="team-container" class="row animated fadeInUp">
			@foreach ($teamMembers as $teamMember)
				<?php
				$descriptionVar = 'teamDescription'.$teamMember['firstName'];
				?>
				<div class="team-member">
					<!-- Avatar -->
					<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
						<img class="avatar img-responsive" src="{{ URL::asset('assets/images/team/'.$teamMember['image']) }}"/>
					</div>
					<!-- Content -->
					<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
						<h3><a href="https://twitter.com/{{ $teamMember['twitter'] }}">{{ $teamMember['firstName'] }}</a></h3>
						{{{ $$descriptionVar }}}
					</div>
				</div>
			@endforeach
		</div>

	</div>
@stop