<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_key',
        'question_group_id',
        'question_type_id',
        'question',
        'description',
        'mandatory',
        'position',
        'reuse_question_options'
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
     * A Question has many Question Options
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionOptions(){
        return $this->hasMany('App\QuestionOption');
    }

    /**
     * A Question has many Form Reply Answers
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formReplyAnswers(){
        return $this->hasMany('App\FormReplyAnswer');
    }    
    
    /**
     * A Question belongs to one Question Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionType(){
        return $this->belongsTo('App\QuestionType');
    }

    /**
     * A Question belongs to one Question Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionGroup(){
        return $this->belongsTo('App\QuestionGroup');
    }

    /**
     * Each Question has many Question Groups through each Form.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */    
    public function formThroughQuestionGroups()
    {
        return $this->hasManyThrough('App\Form', 'App\QuestionGroup');
    }

    /*
     * A Question have many Question Translations.
     */
    public function questionTranslations(){
        return $this->hasMany('\App\QuestionTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\QuestionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('question',$translation[0]->question);
            $this->setAttribute('description',$translation[0]->description);
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
        $translations = $this->hasMany('App\QuestionTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
