@extends('layouts.page')

@section('content')
	<div id="reset-password" class="row animated fadeInUp">
		<!-- RESET A PASSWORD -->
		{{ Form::open(array('url' => URL::action('RemindersController@postRemind'), 'class' => 'form-horizontal')) }}

			<h1><i class="fa fa-lock"></i><span class="red">{{ Lang::get('auth.iveLostMyPassword')}}</span></h1>
			<div class="relax-text">
				{{ Lang::get('auth.lostCheerUp') }}
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

			<!-- Submit button -->
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					{{ Form::submit(Lang::get('auth.resetMyPassword'), array('class' => 'transition btn btn-primary btn-lg')) }}
				</div>
			</div>
		{{ Form::close() }}
	</div>
@stop