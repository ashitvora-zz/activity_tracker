<?php namespace Alfanso\ActivityTracker;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Config;

class Activity extends Eloquent{

  protected $table = "activities";


  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);
  }


  /**
   * Get by User
   */
  public function scopeUser($q, $userId)
  {
    return $q->whereActorId($userId);
  }


  /**
   * Get by Model
   */
  public function scopeModel($q, $modelClass)
  {
    return $q->whereTrackableType($modelClass);
  }


  /**
   * Get by Model Id
   */
  public function scopeId($q, $modelId)
  {
    return $q->whereTrackableId($modelId);
  }


  /**
   * Get by Action
   */
  public function scopeAction($q, $action)
  {
    return $q->whereAction($action);
  }


  /**
   * Trackable
   * Grab the activity history for the model that is calling
   * @return array activity history
   */
  public function trackable()
  {
      return $this->morphTo();
  }


  /**
   * Get Actor of the Activity
   */
  public function actor()
  {
    return $this->belongsTo(Config::get('auth.model'), 'actor_id');
  }
}
