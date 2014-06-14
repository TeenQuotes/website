{{ Form::open(array('route' => array('users.delete'), 'class' => 'form-horizontal animated fadeInUp', 'id' => 'delete-account', 'method' => 'DELETE')) }}
<h2><i class="fa fa-times"></i> {{ Lang::get('users.deleteAccountTitle') }}</h2>

<div class="info-pre-form">
	{{ Lang::get('users.deleteAccountWarning') }}
</div>

<!-- Password -->
<div class="form-group">
	{{ Form::label('password', Lang::get('users.yourPassword'), array('class' => 'col-sm-2 control-label')) }}

	<div class="col-sm-10">
		{{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
		@if (!empty($errors->first('password')))
			{{ TextTools::warningTextForm($errors->first('password')) }}
		@endif
	</div>
</div>

<!-- Delete confirmation -->
<div class="form-group">
	{{ Form::label('delete-confirmation', Lang::get('users.writeDelete'), array('class' => 'col-sm-2 control-label')) }}

	<div class="col-sm-10">
		{{ Form::text('delete-confirmation', Input::old('delete-confirmation'), array('class' => 'form-control', 'id' => 'delete-confirmation')) }}
		@if (!empty($errors->first('delete-confirmation')))
			{{ TextTools::warningTextForm($errors->first('delete-confirmation')) }}
		@endif
	</div>
</div>

<!-- Submit -->
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		{{ Form::submit(Lang::get('users.deleteAccountSubmit'), array('class' => 'transition btn btn-danger btn-lg')) }}
	</div>
</div>
{{ Form::close() }}