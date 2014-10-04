@extends('layouts.page')
@section('content')
	<div id="search-page">
		<div class="animated fadeInUp">
			{{ Form::open(array('url' => URL::route('search.dispatcher'), 'class' => 'form-horizontal')) }}

				<h1><i class="fa fa-search"></i>{{ Lang::get('search.searchTitle')}}</h1>

				<!-- Search query -->
				<div class="form-group">
					{{ Form::label('search', Lang::get('search.searchInput'), array('class' => 'col-sm-4 control-label')) }}

					<div class="col-sm-8">
						{{ Form::text('search', Input::old('search'), array('class' => 'form-control', 'placeholder' => Lang::get('search.searchInputPlaceholder'))) }}
						@if ( ! empty($errors->first('search')))
							{{ TextTools::warningTextForm($errors->first('search')) }}
						@endif
					</div>
				</div>

				<!-- Submit button -->
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-10">
						{{ Form::submit(Lang::get('search.searchSubmit'), ['class' => 'transition btn btn-primary btn-lg']) }}
					</div>
				</div>
			{{ Form::close() }}
		</div>
	</div>
@stop