{{ Form::open(['route' => 'users.delete', 'class' => 'form-horizontal', 'id' => 'delete-account', 'method' => 'DELETE']) }}
	<h2><i class="fa fa-times"></i> {{ Lang::get('users.deleteAccountTitle') }}</h2>

	<div class="info-pre-form">
		{{ Lang::get('users.deleteAccountWarning') }}
	</div>

	<!-- Password -->
	<div class="form-group">
		{{ Form::label('password', Lang::get('users.yourPassword'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::password('password', ['class' => 'form-control', 'id' => 'password']) }}
			@if ( ! empty($errors->first('password')))
				{{ TextTools::warningTextForm($errors->first('password')) }}
			@endif
		</div>
	</div>

	<!-- Delete confirmation -->
	<div class="form-group">
		{{ Form::label('delete-confirmation', Lang::get('users.writeDelete'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::text('delete-confirmation', Input::old('delete-confirmation'), ['class' => 'form-control', 'id' => 'delete-confirmation']) }}
			@if ( ! empty($errors->first('delete-confirmation')))
				{{ TextTools::warningTextForm($errors->first('delete-confirmation')) }}
			@endif
		</div>
	</div>

	<!-- Submit -->
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			{{ Form::submit(Lang::get('users.deleteAccountSubmit'), ['class' => 'transition btn btn-danger btn-lg']) }}
		</div>
	</div>
{{ Form::close() }}
