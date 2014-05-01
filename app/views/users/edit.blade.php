@extends('layouts/page')
@section('content')
<div id="editprofile-page">
	{{ Form::open(array('url' => URL::route('users.update', $login), 'class' => 'form-horizontal', 'method' => 'PUT')) }}


	<div class="form-group {{{ $errors->has('login') ? 'error' : '' }}}">
		{{ Form::label('login', Lang::get('users.genderLabel'), array('class' => 'col-sm-2 control-label')) }}

		<!-- Gender -->
		<div class="col-sm-10">
			<div class="register-switch">
				<input type="radio" name="gender" value="F" id="gender_f" class="register-switch-input" {{{ ($gender == 'M') ? '' : 'checked'}}}>
				<label for="gender_f" class="register-switch-label"><i class="fa fa-female "></i> {{ Lang::get('users.femaleLabel') }}</label>
				<input type="radio" name="gender" value="M" id="gender_m" class="register-switch-input" {{{ ($gender == 'M') ? 'checked' : ''}}}>
				<label for="gender_m" class="register-switch-label"><i class="fa fa-male "></i> {{ Lang::get('users.maleLabel') }}</label>
			</div>
		</div>

		<!-- Birthdate -->
		<div class="form-group {{{ $errors->has('birthdate') ? 'error' : '' }}}">
			{{ Form::label('birthdate', Lang::get('users.birthdateInput'), array('class' => 'col-sm-2 control-label')) }}

			<div class="col-sm-10">
				{{ Form::text('birthdate', Input::old('birthdate'), array('class' => 'form-control', 'placeholder' => Lang::get('users.dateFormatInput'))) }}
				@if (!empty($errors->first('birthdate')))
					{{ TextTools::warningTextForm($errors->first('birthdate')) }}
				@endif
			</div>
		</div>

		<!-- Submit button -->
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit(Lang::get('auth.signupButton'), array('class' => 'transition animated fadeInUp btn btn-primary btn-lg', 'id' => 'submit-form')) }}
			</div>
		</div>
	</div>

	{{ Form::close() }}
</div>
@stop