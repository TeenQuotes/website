			<br/><br/>
			{{ Lang::get('auth.teamFooterEmail') }}<br/>
			<br/>
			<div id="footer-container">
				<br/>
				<img src="{{ Lang::get('email.iconWebsite') }}" class="icon"/>{{ Lang::get('email.footerWebsite') }} <a href="http://{{ Config::get('app.domain') }}" title="Website">{{ Config::get('app.domain') }}</a><br/>
				<img src="{{ Lang::get('email.iconApp') }}" class="icon"/>{{ Lang::get('email.footerApplication') }}: <a href="{{ Lang::get('layout.downloadLinkiOS') }}" title="iPhone">{{ Config::get('app.domain') }}/apps</a><br/>
				<img src="{{ Lang::get('email.iconTwitter') }}" class="icon"/>{{ Lang::get('email.footerTwitter') }} <a href="http://twitter.com/{{str_replace('@', '', Lang::get('layout.twitterUsername'))}}" title="Twitter">{{ Lang::get('layout.twitterUsername') }}</a><br/>
				<img src="{{ Lang::get('email.iconStories') }}" class="icon"/>{{ Lang::get('email.footerTwitter') }} <a href="{{URL::route('stories')}}" title="Stories">{{ Config::get('app.domainStories') }}</a><br/>
			</div>
		</div><!-- BODY CONTENT -->
	</div><!-- CONTAINER -->
</div><!-- BACKGROUND -->

<span id="footer-disclaimer">
	@yield('add-footer')
	<br/><br/>
	{{ Lang::get('email.robotFooter') }}
</span>