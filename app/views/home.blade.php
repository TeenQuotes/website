@include('layouts/header')
<div class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<a href="../" class="logo"></a>
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="navbar-collapse collapse" id="navbar-main">
			<ul class="nav navbar-nav">
				<li>
					<a href="/"><i class="fa fa-user"></i> {{ Lang::get('layout.login') }}</a>
				</li>
				<li>
					<a href="/"><i class="fa fa-random"></i>{{ Lang::get('layout.randomQuotes') }}</a>
				</li>
				<li>
					<a href="/"><i class="fa fa-comment"></i>{{ Lang::get('layout.addQuote') }}</a>
				</li>
				<li>
					<a href="/"><i class="fa fa-mobile fa-lg"></i>{{ Lang::get('layout.apps') }}</a>
				</li>
			</ul>

			<ul class="nav navbar-nav navbar-right hidden-sm hidden-xs">
				<li>
					<a href="http://twitter.com/ohteenquotes" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @ohteenquotes</a>
				</li>
			</ul>

		</div>
	</div>
</div>

@include('layouts/footer')