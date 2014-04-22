@extends('layouts/page')
@section('content')
	{{ Form::open(array('url' => URL::route('signin'), 'class' => 'form-horizontal')) }}

		<!-- Login -->
		<div class="form-group {{{ $errors->has('login') ? 'error' : '' }}}">
			{{ Form::label('login', Lang::get('auth.login'), array('class' => 'col-sm-2 control-label')) }}

			<div class="col-sm-10">
				{{ Form::text('login', Input::old('login'), array('class' => 'form-control')) }}
				@if (!empty($errors->first('login')))
					{{ TextTools::warningTextForm($errors->first('login')) }}
				@endif
			</div>
		</div>

		<!-- Password -->
		<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
			{{ Form::label('password', Lang::get('auth.password'), array('class' => 'col-sm-2 control-label')) }}

			<div class="col-sm-10">
				{{ Form::password('password', array('class' => 'form-control')) }}
				@if (!empty($errors->first('password')))
					{{ TextTools::warningTextForm($errors->first('password')) }}
				@endif
			</div>
		</div>

		<!-- Login button -->
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit(Lang::get('auth.loginButton'), array('class' => 'btn btn-primary btn-lg')) }}
			</div>
		</div>

	{{ Form::close() }}
@stop