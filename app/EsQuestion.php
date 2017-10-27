<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsQuestion extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'question'    
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['start_date','end_date','deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at','event_schedule_id'];
    
    /**
     * Each Question belongs to a Event Schedule.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventSchedule() {
        return $this->belongsTo('App\eventSchedule');
    }        
    
    /**
     * This defines a many-to-many relationship betweens Questions and Participants.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attendances(){
        return $this->belongsToMany('App\Participant', 'es_question_es_participant')->withPivot('attendance');
    }
}
