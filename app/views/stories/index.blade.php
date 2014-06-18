@extends('layouts.stories')

@section('content')
	<div id="stories">
		@foreach ($stories as $story)
			@include('stories.single')
		@endforeach

		<div class="text-center">
			{{ $stories->links() }}
		</div>
	</div>
@stop