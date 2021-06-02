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

class Category extends Model
{
    use SoftDeletes, Cacheable, Filterable;

    protected $cacheTime = 0;

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

    public function scopePublic($query)
    {
        return $query->where([ 
            ['is_active', 1], 
            ['is_public', '!=', 0],
        ]);
    }

    public function scopeApiPublic($query)
    {
        return $query->where([['is_public', 2], ['is_active', 1]]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }
    
	public function product()
	{
			return $this->hasMany(Product::class, 'category_id');
	}
	//Add_hasMany
}
