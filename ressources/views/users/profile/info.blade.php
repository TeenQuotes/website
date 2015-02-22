<div id="profile-info" class="animated fadeInDown">
	<div class="row">
		<!-- Avatar -->
		<div class="avatar-container col-xs-3 col-sm-2">
			<img class="avatar img-responsive" src="{{{ $user->present()->avatarLink }}}"/>
		</div>

		<!-- Users stats -->
		<div class="user-info col-xs-9 col-sm-3">
			<h2> {{{ $user->login }}}</h2>
			<!-- Age -->
			@if( ! is_null($user->birthdate))
				<i class="fa fa-clock-o"></i> {{ $user->present()->age.' '.Lang::get('users.yearsOldAbbreviation') }}<br/>
			@endif

			<!-- City and country -->
			@if ( ! is_null($user->country) OR ! is_null($user->city) AND ( ! empty($user->country) OR ! empty($user->city)))
				<i class="fa fa-map-marker"></i>
				<!-- City -->
				@if ( ! is_null($user->city) AND ! empty($user->city))
					@if( ! is_null($user->country))
						{{{ $user->city }}},
					@else
						{{{ $user->city }}}
					@endif
				@endif

				<!-- Country -->
				@if ( ! is_null($user->country) AND ! empty($user->country))
					@include('countries.partials.mini', ['country' => $user->country_object])
				@endif
				<br/>
			@endif

			<!-- Gender -->
			@if ($user->isMale())
				{{ HTML::image('assets/images/icons/mustach.png', 'Mustach', ['class' => 'mustach']) }}
				{{ Lang::get('users.iAmAMan') }}
			@else
				<i class="fa {{ $user->present()->iconGender }}"></i>
				{{ Lang::get('users.iAmAWoman') }}
			@endif
		</div>

		<!-- Counters -->
		<div class="col-xs-12 col-sm-7">
			@include('users.profile.counters')
			<!-- About me -->
			@if ( ! empty($user->about_me))
				<div class="col-xs-12 about-me hidden-xs hidden-sm">
					{{{ $user->about_me }}}
				</div>
			@endif
		</div>

		<!-- About me -->
		@if ( ! empty($user->about_me))
			<div class="col-xs-12 about-me hidden-md hidden-lg">
				{{{ $user->about_me }}}
			</div>
		@endif
	</div><!-- .row -->
</div>
<div class="clearfix"></div>