@extends('layouts.stories')

@section('content')
	<div id="stories">
		@foreach ($stories as $story)
			@include('stories.single')
		@endforeach

		{{ $stories->links() }}
	</div>
@stop