{{ Lang::get('auth.mustBeLoggedToAddcooment') }}

<div class="row" id="require-log-buttons">
	
	<!-- Signin -->
	<div class="text-center col-sm-6">
		<a href="{{ URL::route('signin') }}" class="transition btn btn-warning btn-lg">{{Lang::get('auth.iHaveAnAccount')}}</a>
	</div>
	
	<!-- Signup -->
	<div class="text-center col-sm-6">
		<a href="{{ URL::route('signup') }}" class="transition btn btn-success btn-lg">{{Lang::get('auth.wantsAnAccount')}}</a>
	</div>
</div>