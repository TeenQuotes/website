<div class="row user-row">
	<!-- Avatar -->
	<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
		<a href="{{ URL::route('users.show', array($user->login)) }}"><img class="avatar img-responsive" src="{{{ $user->present()->avatarLink }}}"/></a>
	</div>

	<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11 user-description">
		<a href="{{ URL::route('users.show', array($user->login)) }}" class="username"><i class="fa {{ $user->present()->iconGender}}"></i> {{{ $user->login }}}</a>

		<!-- Country -->
		@if ( ! is_null($user->country))
			<div class="country hidden-xs">
				{{ $user->country_object->name}}
				@include('countries.partials.flag', ['country' => $user->country_object])
			</div>
		@endif

		<!-- About me -->
		@if ( ! is_null($user->about_me))
			<div class="clearfix"></div>
			<div class="about-me">
				{{{ $user->about_me }}}
			</div>
		@endif
	</div>
</div>