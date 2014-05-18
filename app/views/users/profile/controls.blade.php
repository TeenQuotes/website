<div class="btn-group">
	@if (Auth::check() AND Auth::user()->login == $user->login)
		<a href="{{URL::route('users.edit', $user->login)}}"><button type="button" class="btn btn-warning"><i class="fa fa-edit"></i> {{Lang::get('users.editProfileTitle')}}</button></a>
		<a href="{{URL::route('logout')}}"><button type="button" class="btn btn-danger"><i class="fa fa-sign-out"></i> {{Lang::get('layout.logout')}}</button></a>
	@endif
</div>
<div class="clearfix"></div>