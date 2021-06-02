<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Dwij IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Http\Controllers\Api\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lehungdev\Crmadmin\Models\Module;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Controllers\Api\Properties\PropertyTransformer;

use Validator;
use Exception;

use App\Models\Property;

class PropertyApiController extends Controller
{
    public $table       = 'properties';
    public $page        = 25;
    public $local       = 'vi';
    public $local_id    = NULL;
    protected $listing_cols             = ['id', 'name', 'description', 'value', 'unit', 'type_data', 'filter', 'show_colum', 'user_id', 'updated_at'];
    protected $listing_cols_transform   = ['name', 'description', 'value', 'unit', 'type_data', 'filter', 'show_colum', 'user_id'];

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
        $query = Property::with($with_includes)->filter($request)->select($this->listing_cols)->paginate($limit);

        if (!empty($query)) {
            $listing_item = fractal($query, new PropertyTransformer)->paginateWith(new IlluminatePaginatorAdapter($query))->parseIncludes($with_includes)->toArray();
            return response()->json($listing_item, 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Property does not exist'
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
        if (Module::hasAccess("Properties", "create")) {

            $rules = Module::validateRules("Properties", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'error' => [
                        'message' => $validator->errors()
                    ]
                ], 404);
            }

            $insert_id = Module::insert("Properties", $request);

            return response()->json([
                'success' => [
                    'insert_id' => $insert_id,
                    'message' => 'Create Success'
                ]
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Property does not exist'
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
        $item = Property::findOrFail($id);
        if (isset($item->id)) {
            $parse_includes = $request['include'] ? $request['include'] : '';
            $parse_includes = explode(',', $parse_includes);
            $item = fractal($item, new PropertyTransformer)->parseIncludes($parse_includes);
            return response()->json($item, 200);
        }
        return response()->json([
            'error' => [
                'message' => 'Property does not exist'
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
        if (Module::hasAccess("Properties", "edit")) {

            $rules = Module::validateRules("Properties", $request, true, $id);

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 404);
            }
            $update_id = Module::updateRow("Properties", $request, $id);

            return response()->json([
                'success' => [
                    'update_id' => $update_id,
                    'message' => 'Properties update success'
                ]
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Property does not exist'
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
        if (Module::hasAccess("Properties", "delete")) {
            try {

                $item = Property::findOrFail($id);

                if ($item != null) {
                    $item->delete();
                    return response()->json([
                        'success' => [
                            'delete_id' => $id,
                            'message' => 'Properties delete success'
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'error' => [
                            'delete_id' => $id,
                            'message' => 'Properties not found'
                        ]
                    ], 200);
                }
            } catch (Exception $e) {
                dd('aaa');
            };
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Property does not exist'
                ]
            ], 404);
        }
    }
}
