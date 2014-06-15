	</div><!-- END WRAP -->
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-xs-6">
					<span class="hidden-xs">{{ Lang::get('layout.nameWebsite') }}. </span>{{ Lang::get('layout.catchphrase') }}.
				</div>

				<div class="col-xs-6 right">
					{{ HTML::image('/assets/images/eiffelTower.png', 'Eiffel Tower', array('class' => 'hidden-xs hidden-sm')) }} Designed in France
				</div>
			</div>
		</div>
	</footer><!-- END FOOTER -->

	@include('js/js')

	@yield('add-js')
</body>
</html>