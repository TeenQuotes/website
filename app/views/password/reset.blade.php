@extends('layouts/page')

@section('content')
	<div id="reset-password" class="row animated fadeInUp">
		<!-- RESET A PASSWORD -->
		{{ Form::open(array('url' => URL::action('RemindersController@postReset'), 'class' => 'form-horizontal')) }}

			<!-- Token -->
			{{ Form::hidden('token', $token) }}

			<h1><i class="fa fa-lock"></i><span class="red">{{ Lang::get('auth.iveLostMyPassword')}}</span></h1>
			<div class="relax-text">
				{{ Lang::get('auth.resetCheerUp') }}
			</div>

			<!-- Email address -->
			<div class="form-group">
				{{ Form::label('email', Lang::get('auth.emailAddress'), array('class' => 'col-sm-2 control-label')) }}

				<div class="col-sm-10">
					{{ Form::email('email', Input::old('email'), array('class' => 'form-control')) }}
					@if (Session::has('error'))
						{{ TextTools::warningTextForm(Session::get('error')) }}
					@endif
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('password', Lang::get('auth.password'), array('class' => 'col-sm-2 control-label')) }}

				<div class="col-sm-10">
					{{ Form::password('password', array('class' => 'form-control')) }}
					@if (Session::has('error'))
						{{ TextTools::warningTextForm(Session::get('error')) }}
					@endif
				</div>
			</div>

			<!-- Submit button -->
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					{{ Form::submit(Lang::get('auth.changeMyPasswordButton'), array('class' => 'transition btn btn-primary btn-lg')) }}
				</div>
			</div>
		{{ Form::close() }}
		<div class="contact-human">
			<h2><i class="fa fa-support"></i>{{ $contactHumanTitle }}</h2>
			{{ $contactHumanContent }}
		</div>
	</div>
@stop