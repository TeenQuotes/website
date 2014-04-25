		<div id="push"></div>
	</div><!-- END WRAP -->
	
	<footer>
		<div class="container">

			<div class="row">
				<div class="col-xs-6">
					{{ Lang::get('layout.nameWebsite') }}. {{ Lang::get('layout.catchphrase') }}.
				</div>

				<div class="col-xs-6 right">
					<i class="fa fa-hospital-o"></i> Designed in France
				</div>
		</div>
	</footer><!-- END FOOTER -->

	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if (!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
	{{ HTML::script('assets/js/app.js') }}
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</body>
</html>