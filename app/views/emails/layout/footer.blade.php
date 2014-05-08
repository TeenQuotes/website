			<br/><br/>
			{{ Lang::get('auth.teamFooterEmail') }}<br/>
			<br/>
			<div id="footer-container">
				<br/>
				<img src="{{ Lang::get('email.iconWebsite') }}" class="icon"/>Website: <a href="http://{{ Lang::get('layout.domain') }}" title="Website">{{ Lang::get('layout.domain') }}</a><br/>
				<img src="{{ Lang::get('email.iconApp') }}" class="icon"/>iOS application: <a href="{{ Lang::get('layout.downloadLinkiOS') }}" title="iPhone">{{ Lang::get('layout.domain') }}/apps</a><br/>
				<img src="{{ Lang::get('email.iconTwitter') }}" class="icon"/>Twitter: <a href="http://twitter.com/{{str_replace('@', '', Lang::get('layout.twitterUsername'))}}" title="Twitter">{{ Lang::get('layout.twitterUsername') }}</a><br/>
			</div>
		</div><!-- BODY CONTENT -->
	</div><!-- CONTAINER -->
</div><!-- BACKGROUND -->

<span id="footer-disclaimer">
	@yield('add-footer')
	<br/><br/>
	{{ Lang::get('email.robotFooter') }}
</span>