<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSchedule extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'entity_id',
        'type_id',
        'title',
        'description',
        'local',
        'closed',
        'public',
        'es_question_id',
        'es_period_id',
        'created_by',
        'updated_by'
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
     * Each Event Schedule has many Periods.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function periods() {
        return $this->hasMany('App\EsPeriod');
    }

    /**
     * Each Event Schedule has many Question.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions() {
        return $this->hasMany('App\EsQuestion');
    }
    
    /**
     * Each Event Schedule has many Participants.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants() {
        return $this->hasMany('App\EsParticipant');
    }    
}
