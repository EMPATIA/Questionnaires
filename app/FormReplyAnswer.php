<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormReplyAnswer extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_reply_answer_key',
        'form_reply_id',
        'question_id',
        'question_option_id',
        'answer'
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
     * A Form Reply Answer belongs to one Form Reply
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formReply(){
        return $this->belongsTo('App\FormReply');
    }
    
    /**
     * A Form Reply Answer belongs to one Question
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(){
        return $this->belongsTo('App\question');
    }    
    
}
