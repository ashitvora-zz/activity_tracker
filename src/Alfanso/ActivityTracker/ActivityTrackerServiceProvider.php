<?php namespace Alfanso\ActivityTracker;

use Illuminate\Support\ServiceProvider;
use Event;
use Auth;
use DateTime;

class ActivityTrackerServiceProvider extends ServiceProvider {

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
		$this->package('alfanso/activity-tracker');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		Event::listen('activity.track', function($action = null)
		{
			if(! is_null($action)){
				Activity::insert(array(
					'actor_id' 		=> Auth::user()->id,
					'action'	 		=> $action,
					'created_at'	=> new DateTime,
					'updated_at'	=> new DateTime
				));
			}
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
