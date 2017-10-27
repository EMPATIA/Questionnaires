<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOptionTranslation extends Model
{
    use SoftDeletes;


    protected $fillable=[
        'question_option_id',
        'label',
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
    protected  $hidden = ['id','deleted_at'];

    /**
     * A Question Option Translation as one Question Option
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questions(){
        return $this->belongsTo('\App\QuestionOption');
    }
}
