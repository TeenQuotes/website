<?php
$color = $colors[$i];
$darkColor = Quote::adjustBrightness($colors[$i], -30);
?>

<div class="user-row">
	<!-- COMMENT AND AVATAR -->
	<div class="row">
		<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
			<img class="avatar img-responsive" src="{{{ $user->getURLAvatar() }}}"/>
		</div>

		<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
			<a href="{{ URL::route('users.show', array($user->login)) }}"><i class="fa {{ $user->getIconGender()}}"></i> {{{ $user->login }}}</a>

			@if (!is_null($user->country))
				<div class="country">
					{{ $user->country_object->name}}
				</div>
			@endif

			@if (!is_null($user->about_me))
				<div class="clearfix"></div>
				<div class="about-me">
					{{{ $user->about_me }}}
				</div>
			@endif
		</div>
	</div>
</div>