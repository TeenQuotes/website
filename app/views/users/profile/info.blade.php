<?php
$colorsCounter = Quote::getRandomColors();
if (!is_null($user->birthdate))
	$carbonBirthdate = new Carbon($user->birthdate);
?>
<div id="profile-info">
	<!-- Login -->
	<h2>
		<i class="fa {{$user->getIconGender()}}"></i>
		{{{ $user->login }}}
	</h2>
	<!-- City and country -->
	@if (!is_null($user->country) OR !is_null($user->city) OR !is_null($user->birthdate))
		<div class="city-country-birthday">
			<!-- Age -->
			@if(!is_null($user->birthdate) AND is_null($user->country) AND is_null($user->city))
				{{ $carbonBirthdate->age.' '.Lang::get('users.yearsOldAbbreviation') }}
			@elseif (!is_null($user->birthdate) AND (!is_null($user->country) OR !is_null($user->city)))
				{{ "<span class='birthday'>".$carbonBirthdate->age." ".Lang::get('users.yearsOldAbbreviation')."</span>" }}
			@endif

			<!-- City -->
			@if (!is_null($user->city))
				{{{ $user->city }}}
			@endif
			<!-- Country -->
			@if (!is_null($user->country))
				@if(!is_null($user->city))
				 -
				@endif
				{{{ $user->country_object->name }}}
			@endif
		</div>
	@endif

	<div class="row">
		<!-- Avatar -->
		<div class="column col-xs-4 col-sm-4 col-md-3 col-lg-2">
			<img class="avatar img-responsive" src="{{{ $user->getURLAvatar() }}}"/>
		</div>

		<div class="column col-xs-8 col-sm-8 col-md-9 col-lg-10">
			<!-- About me -->
			@if (!empty($user->about_me))
				<div class="about-section">
					{{{ $user->about_me}}}
				</div>
			@endif

			<!-- Counters -->
			<div class="row stats-counter">
				@foreach (['quotesPublishedCount', 'addedFavCount', 'favCount', 'commentsCount'] as $element)
					@if ($$element > 0)
						<div class="col-xs-6 col-md-3 text-center">
							<div class="content">
								<span class="counter" style="color:<?php echo $colorsCounter[$i]; ?>">{{$$element}}</span>
								{{Lang::choice('users.'.$element.'Text', $$element) }}
							</div>
						</div>
						<?php $i++ ; ?>
					@endif
				@endforeach
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>