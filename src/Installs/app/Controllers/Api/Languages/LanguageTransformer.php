<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Http\Controllers\Api\Languages;

use League\Fractal\TransformerAbstract;



use App\Models\Language;


class LanguageTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [];
    protected $fields = ['id', 'name', 'image', 'locale', 'updated_at'];

    public function transform($results)
    {
        $myNewArray = array_combine($this -> fields, array_map(function($value) use ($results) {
            $value = $results[$value];
            return  $value;
        }, $this -> fields));
        return $myNewArray;
    }

    /* Model belongsTo */


    /* Model belongsTo children*/

    //Add_belongsTo_parent
}
