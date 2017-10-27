<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionTranslation extends Model
{
    use SoftDeletes;

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable =[
        'question_id',
        'question',
        'description',
        'language_code'
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
     * A QuestionTranslations as one Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questions(){
        return $this->belongsTo('App\Question');
    }
}
