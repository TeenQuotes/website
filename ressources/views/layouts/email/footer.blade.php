			<br/><br/>
			{{ Lang::get('auth.teamFooterEmail') }}<br/>
			<br/>
			<div id="footer-container">
				<br/>
				<!-- WEBSITE -->
				<img src="{{ Lang::get('email.iconWebsite') }}" class="icon"/>{{ Lang::get('email.footerWebsite') }} <a href="http://{{ Config::get('app.domain') }}" title="Website">{{ Config::get('app.domain') }}</a><br/>
				<!-- APPS -->
				@if (Config::get('mobile.iOSApp') OR Config::get('mobile.androidApp'))
					<img src="{{ Lang::get('email.iconApp') }}" class="icon"/>{{ Lang::get('email.footerApplication') }} <a href="{{ URL::route('apps.device', 'ios') }}" title="iPhone">{{ URL::route('apps') }}</a><br/>
				@endif
				<!-- TWITTER -->
				<img src="{{ Lang::get('email.iconTwitter') }}" class="icon"/>{{ Lang::get('email.footerTwitter') }} <a href="http://twitter.com/{{str_replace('@', '', Lang::get('layout.twitterUsername'))}}" title="Twitter">{{ Lang::get('layout.twitterUsername') }}</a><br/>
				<!-- STORIES -->
				<img src="{{ Lang::get('email.iconStories') }}" class="icon"/>{{ Lang::get('email.footerStories') }} <a href="{{URL::route('stories')}}" title="Stories">{{ Config::get('app.domainStories') }}</a><br/>
			</div>
		</div><!-- BODY CONTENT -->
	</div><!-- CONTAINER -->
</div><!-- BACKGROUND -->

<span id="footer-disclaimer">
	@yield('add-footer')
</span>