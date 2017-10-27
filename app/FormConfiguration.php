<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormConfiguration extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['form_configuration_key', 'code'];

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
     * This defines a many-to-many relationship between Form Configurations and Forms.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function forms(){
        return $this->belongsToMany('App\Form', 'form_config')
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * A Form Configuration has many Form Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formConfigurationTranslations(){
        return $this->hasMany('App\FormConfigurationTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\FormConfigurationTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
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
        $translations = $this->hasMany('App\FormConfigurationTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
