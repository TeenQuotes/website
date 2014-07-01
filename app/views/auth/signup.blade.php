@extends('layouts/page')
@section('content')
	<div id="signup-page" class="row">
		<div class="animated fadeInRight col-md-6 col-md-push-6">
			<div class="row heart-row">
				<div class="col-xs-4"></div>
				<div class="col-xs-4">
					<span class="heart-container"><i class="fa fa-heart fa-5x"></i></span>
				</div>
				<div class="col-xs-4"></div>
			</div>
			<div class="signup-advantages">
				<ul>
					{{ Lang::get('auth.signupAdvantages') }}
				</ul>
			</div>
		</div>

		<div class="animated fadeInLeft col-md-6 col-md-pull-6">
			<h1><i class="fa fa-user"></i>{{ Lang::get('auth.createYourAccount')}}</h1>
			<div id="signup-text">
				{{Lang::get('auth.signupText')}}
			</div>
			{{ Form::open(array('url' => URL::route('users.store'), 'class' => 'form-horizontal')) }}

				<!-- Login -->
				<div class="form-group {{{ $errors->has('login') ? 'error' : '' }}}">
					{{ Form::label('login', Lang::get('auth.login'), array('class' => 'col-sm-2 control-label')) }}

					<div class="col-sm-10">
						{{ Form::text('login', Input::old('login'), array('class' => 'form-control', 'id' => 'login-signup')) }}
						<div id="login-validator">

						</div>
						@if (!empty($errors->first('login')))
							{{ TextTools::warningTextForm($errors->first('login')) }}
						@endif
					</div>
				</div>

				<!-- Email address -->
				<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
					{{ Form::label('email', Lang::get('auth.emailAddress'), array('class' => 'col-sm-2 control-label')) }}

					<div class="col-sm-10">
						{{ Form::email('email', Input::old('email'), array('class' => 'form-control', 'id' => 'email-signup')) }}
						<div id="respect-privacy">
							{{ Lang::get('auth.carefulPrivacy') }}
						</div>
						@if (!empty($errors->first('email')))
							{{ TextTools::warningTextForm($errors->first('email')) }}
						@endif
					</div>
				</div>

				<!-- Password -->
				<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
					{{ Form::label('password', Lang::get('auth.password'), array('class' => 'col-sm-2 control-label')) }}

					<div class="col-sm-10">
						{{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
						@if (!empty($errors->first('password')))
							{{ TextTools::warningTextForm($errors->first('password')) }}
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