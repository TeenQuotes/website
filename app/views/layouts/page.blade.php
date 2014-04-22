@include('layouts/header')

<div class="container under-navbar">
	@if(Session::has('success'))
    <div class="animated flipInX alert alert-success alert-dismissable">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    	{{ Session::get('success') }}
    </div>
	@endif
	@yield('content')
</div>

@include('layouts/footer')