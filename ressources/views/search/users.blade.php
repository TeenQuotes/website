@extends('layouts.page')

@section('content')
	<div id="search-page">

		{{-- If there were no results, show a banner --}}
		@include('search.partials.redirectToDefaultCountry')

		@foreach ($users as $user)
			@include('search.partials.singleUser', ['user' => $user])
		@endforeach

		<div class="text-center">
			{{ $paginator->links() }}
		</div>
	</div>
@stop