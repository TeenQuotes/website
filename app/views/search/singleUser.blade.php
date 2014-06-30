<div class="row user-row">
	<!-- Avatar -->
	<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
		<a href="{{ URL::route('users.show', array($user->login)) }}"><img class="avatar img-responsive" src="{{{ $user->getURLAvatar() }}}"/></a>
	</div>

	<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
		<a href="{{ URL::route('users.show', array($user->login)) }}" class="username"><i class="fa {{ $user->getIconGender()}}"></i> {{{ $user->login }}}</a>

		<!-- Country -->
		@if (!is_null($user->country))
			<div class="country">
				{{ $user->country_object->name}}
			</div>
		@endif

		<!-- About me -->
		@if (!is_null($user->about_me))
			<div class="clearfix"></div>
			<div class="about-me">
				{{{ $user->about_me }}}
			</div>
		@endif
	</div>
</div>