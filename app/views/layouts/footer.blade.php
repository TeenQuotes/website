	</div><!-- END WRAP -->
	<footer>
		<div class="container">
			<!-- CATCHPHRASE -->
			<div class="row catchphrase">
				<div class="col-xs-6">
					<span class="hidden-xs">{{ Lang::get('layout.nameWebsite') }}. </span>{{ Lang::get('layout.catchphrase') }}.
				</div>

				<div class="col-xs-6 right">
					{{ HTML::image('/assets/images/eiffelTower.png', 'Eiffel Tower', array('class' => 'hidden-xs hidden-sm')) }} Designed in France
				</div>
			</div>

			<!-- ADDITIONAL LINKS -->
			<div class="row links">
				<div class="col-xs-6">
					<a href="{{URL::route('contact')}}">{{ Lang::get('layout.contact') }}</a>
					<a href="{{URL::route('stories')}}">{{ Lang::get('layout.stories') }}</a>
				</div>

				<div class="col-xs-6 right">
					<a href="//blog.{{Config::get('app.domain')}}">{{ Lang::get('layout.blog') }}</a>
					<a href="{{ URL::route('legal.show', 'tos') }}">{{ Lang::get('layout.legalTerms') }}</a>
				</div>
			</div>
		</div>
	</footer><!-- END FOOTER -->

	@include('js/js')

	@yield('add-js')
</body>
</html>