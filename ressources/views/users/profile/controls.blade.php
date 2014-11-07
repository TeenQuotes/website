<div class="btn-group">
		<!-- Edit profile -->
		<a href="{{ URL::route('users.edit', $user->login) }}"><button type="button" class="btn btn-warning"><i class="fa fa-edit"></i>{{ Lang::get('users.editProfileTitle') }}</button></a>
		
		<!-- Log out -->
		<a href="{{ URL::route('logout') }}"><button type="button" class="btn btn-danger"><i class="fa fa-sign-out"></i>{{ Lang::get('layout.logout') }}</button></a>
</div>
<div class="clearfix"></div>