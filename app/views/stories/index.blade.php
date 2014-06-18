@extends('layouts.stories')

@section('content')
	<div id="stories">
		
		<!-- Form to add a story -->
		@if (Auth::check())
			@include('stories.add')
		<!-- Should be logged in -->
		@elseif ($stories->getCurrentPage() == 1)
			<div class="alert-warning no-hide alert alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				{{ $mustBeLogged }}
			</div>
		@endif

		<!-- Display stories -->
		@foreach ($stories as $story)
			@include('stories.single')
		@endforeach

		<!-- Links to pages -->
		<div class="text-center">
			{{ $stories->links() }}
		</div>
	</div>
@stop