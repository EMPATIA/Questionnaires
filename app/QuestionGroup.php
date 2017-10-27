<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroup extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_group_key',
        'form_id',
        'title',
        'description',
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
    protected $hidden = ['id','deleted_at'];

    /**
     * A Question Group has many Questions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(){
        return $this->hasMany('App\Question');
    }

    /**
     * A Question Group as one Form
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form(){
        return $this->belongsTo('App\Form');
    }

    /**
     * A Question Group have many Questions Group Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionGroupTranslations(){
        return $this->hasMany('\App\QuestionGroupTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\QuestionGroupTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
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
        $translations = $this->hasMany('App\QuestionGroupTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
