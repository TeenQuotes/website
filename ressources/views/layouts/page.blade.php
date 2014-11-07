@include('layouts.header')

<div class="container under-navbar">
	
	<!-- Success flash message -->
	@include('layouts.flashMessages.success')

	<!-- Warning flash message -->
	@include('layouts.flashMessages.warning')

	<!-- The content -->
	@yield('content')
</div>

@include('layouts.footer')