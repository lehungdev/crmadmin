<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Dwij IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lehungdev\Crmadmin\Models\Module;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Controllers\Api\Categories\CategoryTransformer;

use Validator;
use Exception;

use App\Models\Category;

class CategoryApiController extends Controller
{
    public $table       = 'categories';
    public $page        = 25;
    public $local       = 'vi';
    public $local_id    = NULL;
    protected $listing_cols             = ['id', 'name', 'parent', 'hierarchy', 'slug', 'image', 'icon', 'property', 'publish', 'updated_at'];
    protected $listing_cols_transform   = ['name', 'parent', 'hierarchy', 'slug', 'image', 'icon', 'property', 'publish'];

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request['limit'] ? $request['limit'] : $this->page;
        $with_includes = $request['with'] ? $request['with'] : '';
        if($with_includes)
            $with_includes = explode(',', $with_includes);
        else  $with_includes  = [];
        $query = Category::with($with_includes)->filter($request)->select($this->listing_cols)->paginate($limit);

        if (!empty($query)) {
            $listing_item = fractal($query, new CategoryTransformer)->paginateWith(new IlluminatePaginatorAdapter($query))->parseIncludes($with_includes)->toArray();
            return response()->json($listing_item, 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Category does not exist'
                ]
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Module::hasAccess("Categories", "create")) {

            $rules = Module::validateRules("Categories", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'error' => [
                        'message' => $validator->errors()
                    ]
                ], 404);
            }

            $insert_id = Module::insert("Categories", $request);

            return response()->json([
                'success' => [
                    'insert_id' => $insert_id,
                    'message' => 'Create Success'
                ]
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Category does not exist'
                ]
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $item = Category::findOrFail($id);
        if (isset($item->id)) {
            $parse_includes = $request['include'] ? $request['include'] : '';
            $parse_includes = explode(',', $parse_includes);
            $item = fractal($item, new CategoryTransformer)->parseIncludes($parse_includes);
            return response()->json($item, 200);
        }
        return response()->json([
            'error' => [
                'message' => 'Category does not exist'
            ]
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Module::hasAccess("Categories", "edit")) {

            $rules = Module::validateRules("Categories", $request, true, $id);

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 404);
            }
            $update_id = Module::updateRow("Categories", $request, $id);

            return response()->json([
                'success' => [
                    'update_id' => $update_id,
                    'message' => 'Categories update success'
                ]
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Category does not exist'
                ]
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Module::hasAccess("Categories", "delete")) {
            try {

                $item = Category::findOrFail($id);

                if ($item != null) {
                    $item->delete();
                    return response()->json([
                        'success' => [
                            'delete_id' => $id,
                            'message' => 'Categories delete success'
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'error' => [
                            'delete_id' => $id,
                            'message' => 'Categories not found'
                        ]
                    ], 200);
                }
            } catch (Exception $e) {
                dd('aaa');
            };
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Category does not exist'
                ]
            ], 404);
        }
    }
}
