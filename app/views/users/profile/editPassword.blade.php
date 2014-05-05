{{ Form::open(array('route' => array('users.password', $user->login), 'class' => 'form-horizontal animated fadeInRight', 'id' => 'edit-password', 'method' => 'PUT')) }}
<h2><i class="fa fa-lock"></i> {{ Lang::get('users.changePasswordTitle') }}</h2>

<div class="info-pre-form">
	{{ Lang::get('users.changePasswordCatchphrase') }}
</div>

<!-- Password -->
<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
	{{ Form::label('password', Lang::get('users.newPasswordInput'), array('class' => 'col-sm-2 control-label')) }}

	<div class="col-sm-10">
		{{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
		@if (!empty($errors->first('password')))
			{{ TextTools::warningTextForm($errors->first('password')) }}
		@endif
	</div>
</div>

<!-- Password confirmation -->
<div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
	{{ Form::label('password_confirmation', Lang::get('users.passwordConfirmationInput'), array('class' => 'col-sm-2 control-label')) }}

	<div class="col-sm-10">
		{{ Form::password('password_confirmation', array('class' => 'form-control')) }}
		@if (!empty($errors->first('password_confirmation')))
			{{ TextTools::warningTextForm($errors->first('password_confirmation')) }}
		@endif
	</div>
</div>

<!-- Submit -->
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		{{ Form::submit(Lang::get('users.changeMyPasswordSubmit'), array('class' => 'transition btn btn-primary btn-lg')) }}
	</div>
</div>
{{ Form::close() }}