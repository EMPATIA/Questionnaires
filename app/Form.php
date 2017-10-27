<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_key',
        'entity_key',
        'title',
        'description',
        'public',
        'start_date',
        'end_date',
        'created_by',
        'link',
        'file_id'

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
     * A Form has many Question Groups.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionGroups(){
        return $this->hasMany('App\QuestionGroup');
    }

    /**
     * A Form has many Form Replies.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formReplies(){
        return $this->hasMany('App\FormReply');
    }

    public function formConfigurations(){
        return $this->belongsToMany('App\FormConfiguration', 'form_config')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function questionsThroughQuestionGroups()
    {
        return $this->hasManyThrough('App\Question', 'App\QuestionGroup');
    }

    /**
     * A Form have many Form Translations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formTranslations(){
        return $this->hasMany('App\FormTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\FormTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\FormTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\FormTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')")->get();
        $this->setAttribute('title',$translation[0]->title);
        $this->setAttribute('description',$translation[0]->description);
    }
}
