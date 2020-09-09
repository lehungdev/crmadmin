<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Http\Controllers\Api\Categories;

use League\Fractal\TransformerAbstract;
use Lehungdev\Crmadmin\Models\Module;
use App\Traits\GetDataLanguage;

use App\Models\Category;

class CategoryTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = ['category', 'children'];
    protected $fields = ['id', 'name', 'parent', 'hierarchy', 'slug', 'image', 'icon', 'property', 'publish', 'updated_at'];

    public function transform($results)
    {
        $module = Module::get('Categories');
        $myNewArray = array_combine($this -> fields, array_map(function($field) use ($results, $module) {
            if(json_decode($results[$field], true)){
                $value = json_decode($results[$field], true);
                if(!empty($module->fields[$field])){
                    if(!empty($module->fields[$field]['lang_active'])){
                        $value = $value[config('app.locale_id')];
                    }
                }
            }
            else $value = $results[$field];
            return  $value;
        }, $this -> fields));
        return $myNewArray;
    }

    /* Model belongsTo */

	public function includeCategory(Category $categories)
	{
		if (!is_null($categories->category)) {
			return $this->item($categories->category, new CategoryTransformer);
		}

	}


    /* Model belongsTo children*/

    public function includeChildren(Category $categories)
	{
		if (!is_null($categories->children)){
			return $this->collection($categories->children, new CategoryTransformer);
		}

	}
    //Add_belongsTo_parent
}
