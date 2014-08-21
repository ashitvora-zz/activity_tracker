<?php namespace Alfanso\ActivityTracker;

use Log;
use Auth;
use DateTime;

trait ActivityTrackerTrait
{

    private $isUpdating;
    private $dirtyFields;
    private $oldData;

    /**
     * Create the event listeners for the saving and saved events
     * This lets us save revisions whenever a save is made, no matter the
     * http method.
     *
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->onSaving();
        });

        static::saved(function ($model) {
            $model->onSave();
        });

        static::deleted(function ($model) {
            $model->onDelete();
        });
    }


    public function activityLog()
    {
        return $this->morphMany('\Alfanso\ActivityTracker\Activity', 'trackable');
    }


    public function activities()
    {
        if($this->id){
            return Activity::where('trackable_type', get_class($this))->where('trackable_id', $this->id)->paginate(20);
        }else{
            return Activity::where('trackable_type', get_class($this))->paginate(20);
        }
    }


    /**
     * Invoked before a model is saved. Return false to abort the operation.
     *
     * @return bool
     */
    public function onSaving()
    {
        // Check if its update
        $this->isUpdating = $this->exists;

        $this->oldData     = $this->original;
        $this->dirtyFields = array_keys($this->getDirty());
    }


    /**
     * Called after a model is successfully saved.
     *
     * @return void
     */
    public function onSave()
    {
        $activities = array(
            'actor_id'       => Auth::user()->id,
            'trackable_type' => get_class($this),
            'action'         => $this->isUpdating ? "updated":"created",
            'trackable_id'   => $this->id,
            'created_at'     => new DateTime(),
            'updated_at'     => new DateTime()
        );

        // Log that model is being updated
        // Else Log that model is being added
        if($this->isUpdating){

            // Return if fields to track are not defined or empty in model
            if(!isset($this->fieldsToTrack) || empty($this->fieldsToTrack)) return;

            $newData    = $this->original;
            $oldData    = $this->oldData;
            $fields     = array();

            foreach ($this->dirtyFields as $field) {

                // if field is in track list then track it otherwise skip inserting log
                if(! in_array($field, $this->fieldsToTrack)) continue;

                if($newData[$field] != $oldData[$field]){
                    $fields[$field] = array(
                        'old_value' => $oldData[$field],
                        'new_value' => $newData[$field]
                    );
                }
            }


            if(empty($fields)) return;

            $activities['details'] = json_encode($fields);

            Activity::insert($activities);

        } else {
            $activities['details'] = $this->toJson();
            Activity::insert($activities);
        }
    }


    /**
     *  Called after a model is successfully deleted.
     */
    public function onDelete()
    {
        Activity::insert(array(
            'actor_id'        => Auth::user()->id,
            'trackable_type'  => get_class($this),
            'trackable_id'    => $this->id,
            'action'          => 'deleted',
            'details'         => $this->toJson(),
            'created_at'      => new DateTime(),
            'updated_at'      => new DateTime(),
        ));
    }
}
