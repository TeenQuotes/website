@extends('layouts.page')

@section('content')
	<h2 id="title-edit-comment"><i class="fa fa-pencil-square-o"></i>{{ Lang::get('comments.updateYourComment') }}</h2>
	{{ Form::model($comment, array('route' => ['comments.update', $comment->id], 'class' => 'form-horizontal animated fadeInUp', 'method' => 'PUT')) }}
	
	<!-- ID of the quote -->
	{{ Form::hidden('quote_id', $comment->quote_id) }}
	
	<!-- Comment's content -->
	<div class="form-group">
		{{ Form::label('content', Lang::get('comments.yourComment'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::textarea('content', Input::old('content'), ['class' => 'form-control', 'rows' => '3', 'id' => 'content-comment']) }}
			<span id="countLetters" class="orange">0 characters</span>
			@if ( ! empty($errors->first('content')))
				{{ TextTools::warningTextForm($errors->first('content')) }}
			@endif
		</div>
	</div>

	<!-- Submit button -->
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			{{ Form::submit(Lang::get('comments.editMyComment'), ['class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-comment']) }}
		</div>
	</div>

	{{ Form::close() }}
@stop