@include('layouts/header')

<div class="container under-navbar">
	@if(Session::has('success'))
		<div class="animated flipInX alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="fa fa-smile-o"></i> {{ Session::get('success') }}
		</div>
	@endif

	@if(Session::has('warning'))
		<div class="animated flipInX alert alert-warning alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="fa fa-meh-o"></i> {{ Session::get('warning') }}
		</div>
	@endif
	@yield('content')
</div>

@include('layouts/footer')