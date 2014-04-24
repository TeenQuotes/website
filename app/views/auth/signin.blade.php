@extends('layouts/page')
@section('content')
	<div id="signin-page" class="row">
		<!-- SIGN IN FORM -->
		<div class="animated fadeInLeft col-md-6">
			{{ Form::open(array('url' => URL::route('signin'), 'class' => 'form-horizontal')) }}

				<h1><i class="fa fa-user"></i><span class="red">{{ Lang::get('auth.welcomeBack')}}</span></h1>
				<div class="signin-text">
					{{ Lang::get('auth.pleasureSeeYouAgain') }}
				</div>
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
						{{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
						@if (!empty($errors->first('password')))
							{{ TextTools::warningTextForm($errors->first('password')) }}
						@endif
					</div>
				</div>

				<!-- Login button -->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						{{ Form::submit(Lang::get('auth.loginButton'), array('class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-form')) }}
					</div>
				</div>
			{{ Form::close() }}
		</div>

		<!-- CALL TO SIGN UP -->
		<div class="animated fadeInRight col-md-6">
			<h1 class="red">{{ Lang::get('auth.wantToBeCool') }}</h1>
			{{ Lang::get('auth.dontOwnAccountYet') }}
			<div class="text-center" id="listener-wants-account">
				<a href="{{URL::route('signup')}}" class="transition btn btn-success btn-lg" id="wants-account">{{Lang::get('auth.wantsAnAccount')}}</a>
			</div>
		</div>
	</div>
@stop