@extends('layouts.page')

@section('content')
	<div id="search-page">
		<h1><i class="fa fa-search"></i> {{ Lang::get('search.resultForQuery', ['query' => $query]) }}</h1>
		<!-- COUNTERS -->
		@include('search.partials.counters')

		<!-- DISPLAYING QUOTES AND USERS RESULTS -->
		@foreach (['quotes', 'users'] as $element)
			@include('search.partials.results')
		@endforeach
	</div>
@stop