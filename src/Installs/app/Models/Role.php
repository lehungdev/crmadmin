<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App;

use Shanmuga\LaravelEntrust\LaravelEntrustRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends LaravelEntrustRole
{
    use SoftDeletes;

	protected $table = 'roles';

	protected $hidden = [

    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
}
