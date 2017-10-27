<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependency extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dependency_key', 'question_option_key', 'question_key'];

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
    protected $hidden = ['id', 'deleted_at'];

        /**
         * A Dependency belongs to one Question Option
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
    public function questionOption(){
        return $this->belongsTo('App\QuestionOption');
    }
}
