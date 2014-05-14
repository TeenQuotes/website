<div class="btn-group">
	@if ($type == 'published')
		<a href="{{URL::route('users.show', $user->login)}}/fav"><button type="button" class="btn btn-success"><i class="fa fa-heart-o"></i> {{Lang::get('users.seeFavoritedQuotes')}}</button></a>
	@else
		<a href="{{URL::route('users.show', $user->login)}}"><button type="button" class="btn btn-success"><i class="fa fa-comment-o"></i> {{Lang::get('users.seePublishedQuotes')}}</button></a>
	@endif
	@if (Auth::check() AND Auth::user()->login == $user->login)
		<a href="{{URL::route('users.edit', $user->login)}}"><button type="button" class="btn btn-warning"><i class="fa fa-edit"></i> {{Lang::get('users.editProfileTitle')}}</button></a>
		<a href="{{URL::route('logout')}}"><button type="button" class="btn btn-danger"><i class="fa fa-sign-out"></i> {{Lang::get('layout.logout')}}</button></a>
	@endif
</div>
<div class="clearfix"></div>