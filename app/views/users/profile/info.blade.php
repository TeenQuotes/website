<?php
$colorsCounter = Quote::getRandomColors();
?>
<div id="profile-info">
	<!-- Login and country -->
	<h2>
		{{{ $user->login }}}
		@if (!is_null($user->country))
			<span class="country">{{{ $user->country_object->name }}}</span>
		@endif
	</h2>

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