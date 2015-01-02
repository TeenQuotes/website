<div class="navbar-collapse collapse" id="navbar-main">
	<ul class="nav navbar-nav">
		<!-- LOGIN -->
		@if (Auth::guest())
			<li>
				<a href="{{ URL::route('signin') }}"><i class="fa fa-user"></i> {{ Lang::get('layout.login') }}</a>
			</li>
		<!-- MY PROFILE -->
		@else
			<li>
				<a href="{{ URL::route('users.show', Auth::user()->login)}}"><i class="fa fa-user"></i> {{ Lang::get('layout.myProfile') }}</a>
			</li>
		@endif

		<!-- RANDOM QUOTES -->
		<li>
			<a href="{{ URL::route('random') }}"><i class="fa fa-random"></i>{{ Lang::get('layout.randomQuotes') }}</a>
		</li>

		<!-- TOP QUOTES -->
		<li>
			<a href="{{ URL::route('quotes.top') }}"><i class="fa fa-bar-chart-o"></i>{{ Lang::get('layout.topQuotes') }}</a>
		</li>

		<!-- ADD QUOTE -->
		<li>
			<a href="{{ URL::route('addquote') }}"><i class="fa fa-comment"></i>{{ Lang::get('layout.addQuote') }}</a>
		</li>

		<!-- SEARCH -->
		<li>
			<a href="{{ URL::route('search.form') }}"><i class="fa fa-search"></i>{{ Lang::get('layout.search') }}</a>
		</li>

		<!-- APPS -->
		@if (Config::get('mobile.iOSApp') OR Config::get('mobile.androidApp'))
			<li class="hidden-sm">
				<a href="{{ URL::route('apps') }}"><i class="fa fa-mobile"></i>{{ Lang::get('layout.apps') }}</a>
			</li>
		@endif
	</ul>

	<!-- TWITTER FOLLOW BUTTON -->
	<ul class="nav navbar-nav navbar-right hidden-sm hidden-xs hidden-md">
		<li>
			<a href="http://twitter.com/ohteenquotes" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @ohteenquotes</a>
		</li>
	</ul>
</div>