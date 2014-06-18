@include('layouts/header')

<div class="container stories-container">
	
	<!-- Hero image -->
	@if (!$heroHide)
		<div id="hero">
			{{ HTML::image('assets/images/stories/hero.jpg', "Hero") }}
			<div class="text">
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