<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Dwij IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;
    
    protected $table = 'languages';
    
    protected $hidden = [
    
    ];
    
    protected $guarded = [];
    
    protected $dates = ['deleted_at'];
}
