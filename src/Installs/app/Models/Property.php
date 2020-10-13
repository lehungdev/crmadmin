<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Cacheable;
use App\Traits\Filterable;

class Property extends Model
{
    use SoftDeletes, Cacheable, Filterable;

    protected $cacheTime = 80;

    protected $table = 'properties';

    protected $hidden = [

    ];

    protected $guarded = [];

    protected $dates = ['deleted_at'];


	public function user()
	{
			return $this->belongsTo(User::class, 'user_id');
	}


	public function category()
	{
			return $this->hasMany(Category::class, 'property');
	}

    //Add_hasMany
}
