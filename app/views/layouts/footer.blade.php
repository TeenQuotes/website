	</div><!-- END WRAP -->
	<footer>
		<div class="container">
			<!-- ADDITIONAL LINKS -->
			<div class="col-sm-6 links">
				<a href="{{URL::route('contact')}}">{{ Lang::get('layout.contact') }}</a>
				<a href="{{URL::route('stories')}}">{{ Lang::get('layout.stories') }}</a>
				<a href="//blog.{{Config::get('app.domain')}}">{{ Lang::get('layout.blog') }}</a>
				<a href="{{ URL::route('legal.show', 'tos') }}">{{ Lang::get('layout.legalTerms') }}</a>
			</div>

			<!-- CATCHPHRASE -->
			<div class="col-sm-6 right">
					{{ HTML::image('/assets/images/eiffelTower.png', 'Eiffel Tower', array('class' => 'hidden-xs hidden-sm')) }}
					<span class="designed">Designed in France</span><br/>
					{{ Lang::get('layout.catchphrase') }}
			</div>
		</div>
	</footer><!-- END FOOTER -->

	@include('js.js')

	@yield('add-js')
</body>
</html>