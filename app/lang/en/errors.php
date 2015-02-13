<?php
return array(
	'defaultNotFound' => "
			<div class='animated fadeInLeft'>
				<h2 class='red'>This :resource:</h2>
				<ul>
					<li>Does not exist.</li>
					<li>No longer exists.</li>
					<li>Had never existed (even if you're dreaming).</li>
					<li>Will probably never exist.</li>
				</ul>
			</div>
			<div class='animated fadeInRight'>
				<h2 class='orange'>You can:</h2>
				<ul>
					<li>Cry.</li>
					<li>Run fast and far.</li>
					<li>Lie on the ground, roll into a ball and moan pitifully.</li>
					<li>Shouting at someone to pick you up, even if nobody will hear you.</li>
				</ul>
			</div>
			<div class='animated fadeInUp'>
				<h2 class='green'>But the best is:</h2>
				<ul>
					<li>Click on the previous page button in your browser.</li>
					<li>Click <a href='".URL::route('home')."'>here</a> to return to home.</li>
				</ul>
			</div>",

	'hiddenProfileBody'      => "Oops, it appears that the profile of :login is hidden! It means that only :login can see this profile.<br/><br/>We take your privacy very seriously and you can hide your profile if you don't want to open your information on ".Lang::get('layout.nameWebsite')." to others.",
	'hiddenProfileTitle'     => 'This profile is hidden!',
	'pageNotFoundPageTitle'  => 'Page not found | '.Lang::get('layout.nameWebsite'),
	'pageNotFoundTitle'      => 'Page not found!',
	'pageText'               => 'page',
	'quoteNotFoundPageTitle' => 'Quote not found | '.Lang::get('layout.nameWebsite'),
	'quoteNotFoundTitle'     => 'Quote not found!',
	'quoteText'              => 'quote',
	'storyNotFoundPageTitle' => 'Story not found | '.Lang::get('layout.nameWebsite'),
	'storyNotFoundTitle'     => 'Story not found!',
	'storyText'              => 'story',
	'tagNotFoundPageTitle'   => 'Tag not found | '.Lang::get('layout.nameWebsite'),
	'tagNotFoundTitle'       => 'Tag not found!',
	'tagText'                => 'tag',
	'tokenNotFoundPageTitle' => 'Token not found | '.Lang::get('layout.nameWebsite'),
	'tokenNotFoundTitle'     => 'Token not found!',
	'tokenText'              => 'token',
	'userNotFoundPageTitle'  => 'User not found | '.Lang::get('layout.nameWebsite'),
	'userNotFoundTitle'      => 'User not found!',
	'userText'               => 'user',
);