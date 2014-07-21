<?php
$addClass = Session::has('requireLoggedInAddQuote') ? ' col-md-push-6' : '';
?>
<!-- CALL TO SIGN UP -->
<div class="animated fadeInRight col-md-6<?= $addClass ?>">
	<h1>{{ Lang::get('auth.wantToBeCool') }}</h1>
	{{ Lang::get('auth.dontOwnAccountYet') }}
	<div class="text-center" id="listener-wants-account">
		<a href="{{URL::route('signup')}}" class="transition btn btn-success btn-lg" id="wants-account">{{Lang::get('auth.wantsAnAccount')}}</a>
	</div>
</div>