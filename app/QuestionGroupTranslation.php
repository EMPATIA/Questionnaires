<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroupTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_group_id',
        'title',
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
    protected $hidden = ['id','deleted_at'];


    /**
     * A Question Group Translation as one Question Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionGroups(){
        return $this->belongsTo('\App\QuestionGroup');
    }
}
