@include('layouts/header')

<div class="container stories-container">
	
	<!-- Hero image -->
	@if (!$heroHide)
		<div id="hero" class="animated fadeInDown">
			{{ HTML::image('assets/images/stories/hero.jpg', "Hero") }}
			<div class="text animated fadeInLeft">
				{{ $heroText }}
				<span class="hidden-xs hidden-sm">
					{{ $tellUsYourStory }}
				</span>
			</div>
		</div>
	@endif

	@yield('content')
</div>

@include('layouts/footer')