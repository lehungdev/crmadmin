<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Cacheable;
use App\Traits\Filterable;

class Language extends Model
{
    use SoftDeletes, Cacheable, Filterable;

    protected $cacheTime = 80;

    protected $table = 'languages';

    protected $hidden = [

    ];

    protected $guarded = [];

    protected $dates = ['deleted_at'];



    //Add_hasMany
}
