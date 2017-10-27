<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsParticipant extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'name',
        'user_key',        
        'event_schedule_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];
    
    /**
     * Each Participant belongs to a Event Schedule.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventSchedule() {
        return $this->belongsTo('App\eventSchedule');
    }    
    
    /**
     * Each Participant belongs to many Periods.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function periods() {
        return $this->belongsToMany('App\EsPeriod')->withPivot('attendance');
    }  

    
    /**
     * Each Participant belongs to many Questions.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function questions() {
        return $this->belongsToMany('App\EsQuestion')->withPivot('attendance');
    }      
}
