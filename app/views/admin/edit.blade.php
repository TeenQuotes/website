@extends('layouts.page')

@section('content')
	<div id="admin-page">
		<h2>Update quote <span>#{{{ $quote->id }}}</span></h2>
		{{ Form::model($quote, array('route' => array('admin.quotes.update', $quote->id), 'class' => 'form-horizontal animated fadeInUp', 'method' => 'PUT')) }}

		<!-- Quote's content -->
		<div class="form-group">
			{{ Form::label('content', 'Content of the quote', array('class' => 'col-sm-2 control-label')) }}

			<div class="col-sm-10">
				{{ Form::textarea('content', Input::old('content'), array('class' => 'form-control', 'rows' => '3')) }}
				@if ( ! empty($errors->first('content')))
					{{ TextTools::warningTextForm($errors->first('content')) }}
				@endif
			</div>
		</div>

		<!-- Submit button -->
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit('Edit this quote!', array('class' => 'transition btn btn-primary btn-lg')) }}
			</div>
		</div>

		{{ Form::close() }}
	</div>
@stop