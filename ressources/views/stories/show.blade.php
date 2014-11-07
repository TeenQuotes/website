@extends('layouts.stories')

@section('content')
	<div id="stories">
		<h2><i class="fa fa-pencil"></i> {{ $storyTitle }} #{{ $story->id }}</h2>
		
		@include('stories.single')

		<a href="{{ URL::previous() }}" class="btn btn-default">&laquo; {{ $goBack }}</a>
	</div>
@stop