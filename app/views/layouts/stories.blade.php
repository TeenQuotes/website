@include('layouts/header')

<div class="container stories-container">
	<div id="hero">
		{{ HTML::image('assets/images/stories/hero.jpg', "Hero") }}
		<div class="text">
			{{ $heroText }}
		</div>
	</div>

	@yield('content')
</div>

@include('layouts/footer')