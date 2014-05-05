				<br/><br/>
				{{ Lang::get('auth.teamFooterEmail') }}<br/>
				<br/>
				<div style="padding-bottom:10px;border-top:1px solid #CCCCCC">
					<br/>
					<img src="{{ Lang::get('email.iconWebsite') }}" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Website: <a href="http://teen-quotes.com" title="Website">{{ Lang::get('layout.domain') }}</a><br/>
					<img src="{{ Lang::get('email.iconApp') }}" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>iOS application: <a href="{{ Lang::get('layout.downloadLinkiOS') }}" title="iPhone">{{ Lang::get('layout.domain') }}/apps</a><br/>
					<img src="{{ Lang::get('email.iconTwitter') }}" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Twitter: <a href="http://twitter.com/{{str_replace('@', '', Lang::get('layout.twitterUsername'))}}" title="Twitter">{{ Lang::get('layout.twitterUsername') }}</a><br/>
				</div>
			</div>
		</div>
	</div>
<br/><br/>

<span style="font-size:90%">
	@yield('add-footer')
	<br/><br/>
	{{ Lang::get('email.robotFooter') }}
</span>