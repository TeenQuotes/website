<?php namespace TeenQuotes\Comments;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Comments\Observers\CommentObserver;

class CommentsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerObserver();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerBindings();
		$this->registerComposers();
		$this->registerRoutes();
	}

	private function registerBindings()
	{
		$namespace = 'TeenQuotes\Comments\Repositories';
		
		$this->app->bind(
			$namespace.'\CommentRepository',
			$namespace.'\DbCommentRepository'
		);
	}

	private function registerComposers()
	{
		$namespace = 'TeenQuotes\Comments\Composers';

		// When editing a comment
		$this->app['view']->composer([
			'comments.edit'
		], $namespace.'\EditComposer');
	}

	private function registerObserver()
	{
		Comment::observe(new CommentObserver);
	}

	private function registerRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->resource('comments', 'CommentsController', ['only' => ['store', 'destroy', 'update', 'edit']]);
		});
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domain'),
			'namespace' => 'TeenQuotes\Comments\Controllers',
		];
	}
}