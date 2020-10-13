<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Str;
use Auth;
use DB;
use Validator;
use Datatables;
use LaravelEntrust;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;

use App\Models\Category;
use App\Models\Language;

class CategoryController extends Controller
{
    public $show_action = true;

    /**
     * Display a listing of the Categories.
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        
        $module = Module::get('Categories');
        $getAllCategory = Category::with(['category','children'])->public()->orderBy('hierarchy', 'asc')->get(); //->orderBy('parent', 'asc')
        $url = $request->url();
        if(Module::hasAccess($module->id)) {
            return View('crm.categories.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => Module::getListingColumns('Categories'),
                'module' => $module,
                'allCategory' => $getAllCategory
            ]);
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new category.
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created category in database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Categories", "create")) {

            $module = Module::get('Categories');
            $pvd_language = Language::get();

            foreach($module->fields as $key_field => $field){
                if(!empty($field['lang_active']) and isset($request->$key_field)){
                    if($field['label'] == 'Image'){
                        $value_image = [];
                        foreach ($pvd_language as $key_lang => $value_lang){
                            $field_image = $key_field.'_'.$value_lang->id;
                            $value_image[$value_lang->id] = $request->$field_image;
                            unset($request->$field_image);
                        }
                        $request->merge([$key_field=>$value_image]);
                    } else {
                        $request->merge([$key_field=>json_encode($request->$key_field, JSON_UNESCAPED_UNICODE)]);
                    }

                } else {
                    if(isset($request->$key_field)){ // and isset($request->$key_field[config('app.locale_id')])
                        $request->merge([$key_field=>$request->$key_field[config('app.locale_id')]]) ;
                    } else {
                        if($field['label'] == 'Image'){
                            $field_image = $key_field.'_'.config('app.locale_id');
                            $request->merge([$key_field=>$request->$field_image]) ;
                            unset($request->$field_image);
                        } else $request->merge([$key_field=>$request->$key_field]);
                    }
                }
            }
            // dd($request->all());
            $rules = Module::validateRules("Categories", $request);

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            //Active and public
            if(!LaravelEntrust::hasRole("SUPER_ADMIN")){
                if($request->is_active == 1){
                    $request->merge(['is_public'=>1]);
                } else $request->merge(['is_public'=>0]);
            }
        
			$request->merge(['user_id'=>Auth::user()->id]);
            $insert_id = Module::insert("Categories", $request);

            return redirect()->route(config('crmadmin.adminRoute') . '.categories.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified category.
     *
     * @param int $id category ID
     * @return mixed
     */
    public function show($id)
    {
        if(Module::hasAccess("Categories", "view")) {

            $category = Category::find($id);
            if(isset($category->id)) {
                $module = Module::get('Categories');
                $module->row = $category;

                return view('crm.categories.show', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                    'no_header' => true,
                    'no_padding' => "no-padding"
                ])->with('category', $category);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("category"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param int $id category ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        if(Module::hasAccess("Categories", "edit")) {
            $category = Category::with(['category', 'children'])->find($id);
            if(isset($category->id)) {
                $module = Module::get('Categories');

                $module->row = $category;

                return view('crm.categories.edit', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                ])->with('category', $category);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("category"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified category in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id category ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Categories", "edit")) {
            $module = Module::get('Categories');
            $pvd_language = Language::get();
            // dd($request);
            foreach($module->fields as $key_field => $field){
                if(!empty($field['lang_active']) and isset($request->$key_field)){
                    if($field['label'] == 'Image'){
                        $value_image = [];
                        foreach ($pvd_language as $key_lang => $value_lang){
                            $field_image = $key_field.'_'.$value_lang->id;
                            $value_image[$value_lang->id] = $request->$field_image;
                            unset($request->$field_image);
                        }
                        $request->merge([$key_field=>$value_image]);
                    } else {
                        $request->merge([$key_field=>json_encode($request->$key_field, JSON_UNESCAPED_UNICODE)]);
                    }

                } else {
                    if(isset($request->$key_field)){ // and isset($request->$key_field[config('app.locale_id')])
                        $request->merge([$key_field=>$request->$key_field[config('app.locale_id')]]) ;
                    } else {
                        if($field['label'] == 'Image'){
                            $field_image = $key_field.'_'.config('app.locale_id');
                            $request->merge([$key_field=>$request->$field_image]) ;
                            unset($request->$field_image);
                        } else $request->merge([$key_field=>$request->$key_field]);
                    }

                }
            }
            $rules = Module::validateRules("Categories", $request, true, $id); 
            $validator = Validator::make($request->all(), $rules);  //dd($validator);
            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }

            $item = Category::find($id);
            //Active and public
            if(!LaravelEntrust::hasRole("SUPER_ADMIN")){
                if($request->is_active == 1){
                    $request->merge(['is_public'=>1]);
                } else $request->merge(['is_public'=>0]);
            }
            $request->merge(['user_id'=>Auth::user()->id]);
            $insert_id = Module::updateRow("Categories", $request, $id);

            return redirect()->route(config('crmadmin.adminRoute') . '.categories.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param int $id category ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if(Module::hasAccess("Categories", "delete")) {
            Category::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('crmadmin.adminRoute') . '.categories.index');
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Server side Datatable fetch via switch
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtswitch(Request $request)
    {
        if(Module::hasFieldAccess("Categories", $request->switchName, "write")){
            if($request->state == "true" || $request->state == 1) {
                $state = 1;
            } else {
                $state = 0;
            }
            $item = Category::find($request->switchId);
            if(isset($item->id)) {
                $item[$request['switchName']] = $state;
                $item->save();
                return response()->json(['status' => 'success', 'message' => "Category field switch ". $request->switchName . $item[$request['switchName']]. " saved to " . $state]);
            } else {
                return response()->json(['status' => 'failed', 'message' => "Category field not found"]);
            }
        }
    }


    /**
     * Server side Datatable fetch via slide switch
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtSlideSwitch(Request $request)
    {
        if(Module::hasFieldAccess("Categories", $request->sliderField, "write")){
            $item = Category::find($request->sliderId); //dd($item->id);
            if(isset($item->id)) {
                $item[$request['sliderField']] = $request->sliderValue;
                $item->save();
                return response()->json(['status' => 'success', 'message' => "Category field  slide switch ". $request->sliderField ." saved to " . $request->sliderId.$item[$request['sliderField']] ]);
            } else {
                return response()->json(['status' => 'failed', 'message' => "Category field not found"]);
            }
        }
    }
    /**
     * Update Menu Hierarchy
     *
     * @return \Illuminate\Http\Response
     */
    public function update_hierarchy(Request $request)
    {
        $parents = $request->jsonData;
        $parent_id = NULL;
        for ($i=0; $i < count($parents); $i++) {
            $this->apply_hierarchy($parents[$i], $i+1, $parent_id);
        }
        return $parents;
    }

    function apply_hierarchy($menuItem, $num, $parent_id = NULL)
    {
        // return "apply_hierarchy: ".json_encode($menuItem)." - ".$num." - ".$parent_id."  <br><br>\n\n";

        if(empty($parent_id)) $parent_id = NULL;
        $menu = Category::find($menuItem['id']);
        $menu->parent = $parent_id;
        $menu->hierarchy = $num;
        $menu->save();

        if(isset($menuItem['children'])) {
            for ($i=0; $i < count($menuItem['children']); $i++) {
                $this->apply_hierarchy($menuItem['children'][$i], $i+1, $menuItem['id']);
            }
        }
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request)
    {
        if(Module::hasAccess("Categories", "edit")) {
            $status = $request->is_public;
            $menu_id = $request->menu_id;

            $insert_id = Module::updateRow("Categories", $request, $menu_id);

            return redirect()->route(config('laraadmin.adminRoute') . '.categories.index');
        }
        else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }
    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtajax(Request $request)
    {
        $module = Module::get('Categories');
        $listing_cols = Module::getListingColumns('Categories');
        // dd($listing_cols);

        $values = DB::table('categories')->select($listing_cols)->whereNull('deleted_at')->limit(2500);
        $out = \DataTables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Categories');

        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] =(array)$data->data[$i];
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];

                //Set value langue
                if(!empty($module->fields[$col]) and !empty($module->fields[$col]['lang_active'])){
                    $data_col = json_decode(str_replace('&quot;', '"', $data->data[$i][$col]), true);
                    if(!empty($data_col)){
                        $data->data[$i][$col] = $data_col[config('app.locale_id')];
                    }
                }//End set value langue

                $data->data[$i][$j] = $data->data[$i][$col];
                if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
                    $fieldValue = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$col]);
                    if(isset($fieldValue)){
                        $data_col = json_decode(str_replace('&quot;', '"', $fieldValue), true);
                        if(!empty($data_col) and isset($data_col[config('app.locale_id')])){
                            $data->data[$i][$j] = '<small class="label label-primary"><i class="fa fa-caret-right"></i> '. $data_col[config('app.locale_id')] .'</small> ';
                        } else  $data->data[$i][$j] = '<small class="label label-primary"><i class="fa fa-caret-right"></i> '. $fieldValue .'</small> ';
                    }
                    else $data->data[$i][$j] = '';
                }
                if($col == $module->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('crmadmin.adminRoute') . '/categories/' . $data->data[$i][$listing_cols[0]]) . '">' . $data->data[$i][$col] . '</a>';
                }
                //Path field Image
                 if($fields_popup[$col] != null && $fields_popup[$col]->field_type_str == "Image") {
                    if($data->data[$i][$col]){
                        $json_image = str_replace('&quot;', '"', $data->data[$i][$col]);
                        $json_image = json_decode($json_image);
                        $data->data[$i][$j] = '<img src="'.url('/s50x50'.$json_image->path).'">';
                    } else {
                        $data->data[$i][$j] = "";
                    }
                }
                //Field CheckBox
                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 2){
                    if(Module::hasFieldAccess("Categories", $col, "write")){
                        if(!empty($data->data[$i][$col])){
                            $switch = 'Off';
                        } else $switch = 'On';
                        $data->data[$i][$j] = '<div class="Switch Ajax '. $col .' Round '. $switch .'" switchName="'. $col .'" " switchId="'. $data->data[$i][$listing_cols[0]] .'" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>';
                    } else {
                        if(!empty($data->data[$i][$col])){
                            if($data->data[$i][$col] == 1){
                                $classStatus = 'success';
                                $status = $module->fields[$col]['colname'];
                            } else{
                                $classStatus = 'warning';
                                $status = 'Chờ '. $module->fields[$col]['colname'];
                            }
                        } else {
                            $classStatus = 'danger';
                            $status = 'Not '. $module->fields[$col]['colname'];
                        }
                        $data->data[$i][$j] = '<small class="label label-'. $classStatus .'">'. Str::ucfirst($status) .'</small> ';
                    }
                }
                //Field Multiselect
                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 15){
                    
                    $popup_vals = str_replace('@', '', $module->fields[$col]['popup_vals']);
                    $json = str_replace('&quot;', '"', $data->data[$i][$col]);
                    $values_json = DB::table($popup_vals)->whereIn('id',json_decode($json))->pluck('name');
                    $data->data[$i][$j] = '';
                    foreach($values_json as $value_json){
                        //Set value langue
                        if(!empty($value_json) and is_array((array)$value_json)){
                            $value_json = json_decode(str_replace('&quot;', '"', $value_json), true);
                            $value_json = $value_json[config('app.locale_id')];
                        }//End set value langue
                        // dd($value_json);
                        $data->data[$i][$j] .= '<small class="label label-primary">'. $value_json .'</small> ';
                    }
                }

                // if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 20){
                //     $values_json = json_decode(str_replace('&quot;', '"', $data->data[$i][$col]), true);
                //     $data->data[$i][$j] = '';
                //     foreach($values_json as $value_json){
                //         $data->data[$i][$j] .= '<small class="label label-primary">'. $value_json .'</small> ';
                //     }
                // }

                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 20){
                    if(!is_array($data->data[$i][$col])){
                        $values_json =  json_decode(str_replace('&quot;', '"', $data->data[$i][$col]), true);
                    }
                    else {
                        $values_json = $data->data[$i][$col];
                    }
                    $data->data[$i][$j] = '';
                    if(is_array($values_json)){
                        foreach($values_json as $value_json){
                            $data->data[$i][$j] .= '<small class="label label-primary">'. $value_json .'</small> ';
                        }
                    } else {
                        $data->data[$i][$j] .= $data->data[$i][$j] .= '<small class="label label-primary">'. $values_json .'</small> ';
                    }
                }

                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 27){
                    $data->data[$i][$j] = '';
                    $data->data[$i][$j] .= '<div style="max-width: 125px;"><input type="text" name="'. $col .'" value="'.$data->data[$i][$col].'" data-slider-value="'.$data->data[$i][$col].'" class="slider form-control" data-slider-min="0" data-slider-max="2" data-slider-step="1" data-slider-orientation="horizontal"  data-slider-id="'.$data->data[$i][$listing_cols[0]].'"></div>';
                }

                if( $col!== 'id')
                    unset($data->data[$i][$col]);
            }

            if($this->show_action) {
                $output = '';
                if(Module::hasAccess("Categories", "edit")) {
                    $output .= '<a href="' . url(config('crmadmin.adminRoute') . '/categories/' . $data->data[$i][$listing_cols[0]] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if(Module::hasAccess("Categories", "delete")) {
                    $output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.categories.destroy', $data->data[$i][$listing_cols[0]]], 'method' => 'delete', 'style' => 'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string)$output;
            }
            unset($data->data[$i]['id']);
        }
        unset($data->queries);
        $out->setData($data);
        return $out;
    }
}
