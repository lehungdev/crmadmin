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

class Category extends Model
{
    use SoftDeletes, Cacheable, Filterable;

    protected $cacheTime = 80;

    protected $table = 'categories';

    protected $hidden = [

    ];

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d',
    ];

    protected $guarded = [];

    protected $dates = ['deleted_at'];


	public function category()
	{
			return $this->belongsTo(Category::class, 'parent');
	}


	public function children()
	{
			return $this->hasMany(Category::class, 'parent');
    }

    public function scopePublish($query)
    {
        return $query->where('publish', '=', 1);
    }

    //Add_hasMany
}
