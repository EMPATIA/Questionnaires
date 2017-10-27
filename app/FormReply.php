<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormReply extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_reply_key',
        'form_id',
        'location',
        'created_by'
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
     * A Form Reply belongs to one Form
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form(){
        return $this->belongsTo('App\Form');
    }

    /**
     * A Form Reply has many Form Reply Answers
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formReplyAnswers(){
        return $this->hasMany('App\FormReplyAnswer');
    }
}
