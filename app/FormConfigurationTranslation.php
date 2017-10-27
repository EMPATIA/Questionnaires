<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormConfigurationTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['form_configuration_id', 'language_code', 'name', 'description'];

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
     * A Form Configuration Translation belongs to one Form Configuration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formConfiguration(){
        return $this->belongsTo('App\FormConfiguration');
    }
}
