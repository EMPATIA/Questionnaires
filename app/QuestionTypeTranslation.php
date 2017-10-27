<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionTypeTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_type_id',
        'name',
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
    protected $hidden = ['id', 'delete_at'];


    /**
     * A Question Type Translations as one Question Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionTypes(){
        return $this->belongsTo('\App\QuestionType');
    }
}
