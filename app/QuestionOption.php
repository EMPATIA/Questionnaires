<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOption extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_option_key',
        'icon_id',
        'question_key',
        /* 'label', */
        'position'
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
     * A Question Option belongs to a Question
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(){
        return $this->belongsTo('App\Question');
    }

    /**
     * A Question Option as one Icon
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon(){
        return $this->belongsTo('App\Icon');
    }

    /**
     * A Question Option has many Dependencies
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dependencies(){
        return $this->hasMany('App\Dependency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionOptionTranslations(){
        return $this->hasMany('\App\QuestionOptionTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\QuestionOptionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('label',$translation[0]->label);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\QuestionOptionTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
