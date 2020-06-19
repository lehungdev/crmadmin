<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace Lehungdev\Crmadmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

use Lehungdev\Crmadmin\Models\Menu;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Lehungdev\Crmadmin\Models\ModuleFieldTypes;
use Lehungdev\Crmadmin\Helpers\LAHelper;

/**
 * Class MenuController
 * @package Lehungdev\Crmadmin\Controllers
 *
 * Works after managing Menus and their hierarchy
 */
class MenuController extends Controller
{
    public function __construct()
    {
        // for authentication (optional)
        // $this->middleware('auth');
    }
    
    /**
     * Display a listing of Menus
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = Module::all();
        // Send Menus with No Parent to Views
        $menuItems = Menu::where("parent", 0)->orderBy('hierarchy', 'asc')->get();
        
        return View('la.menus.index', [
            'menus' => $menuItems,
            'modules' => $modules
        ]);
    }
    
    /**
     * Store a newly created Menu in Database
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty(Input::get('menu_name')))
            $name = Input::get('menu_name');
        else $name = Input::get('name');
        $url = Input::get('url');
        $icon = Input::get('icon');
        $type = Input::get('type');
        
        if($type == "module") {
            $module_id = Input::get('module_id');
            $module = Module::find($module_id);
            if(isset($module->id)) {
                $name = $module->name;
                $url = $module->name_db;
                $icon = $module->fa_icon;
            } else {
                return response()->json([
                    "status" => "failure",
                    "message" => "Module does not exists"
                ], 200);
            }
        }
        Menu::create([
            "name" => $name,
            "url" => $url,
            "icon" => $icon,
            "type" => $type,
            "parent" => 0
        ]);
        if($type == "module") {
            return response()->json([
                "status" => "success"
            ], 200);
        } else {
            return redirect(config('crmadmin.adminRoute') . '/la_menus');
        }
    }
    
    /**
     * Update Custom Menu
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!empty(Input::get('menu_name')))
            $name = Input::get('menu_name');
        else $name = Input::get('name');
        $url = Input::get('url');
        $icon = Input::get('icon');
        $type = Input::get('type');
        
        $menu = Menu::find($id);
        $menu->name = $name;
        $menu->url = $url;
        $menu->icon = $icon;
        $menu->save();
        
        return redirect(config('crmadmin.adminRoute') . '/la_menus');
    }
    
    /**
     * Remove the specified Menu from database
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Menu::find($id)->delete();
        
        // Redirecting to index() method for Listing
        return redirect()->route(config('crmadmin.adminRoute') . '.la_menus.index');
    }
    
    /**
     * Update Menu Hierarchy
     *
     * @return mixed
     */
    public function update_hierarchy()
    {
        $parents = Input::get('jsonData');
        $parent_id = 0;
        
        for($i = 0; $i < count($parents); $i++) {
            $this->apply_hierarchy($parents[$i], $i + 1, $parent_id);
        }
        
        return $parents;
    }
    
    /**
     * Save Menu hierarchy Recursively
     *
     * @param $menuItem Menu Item Array
     * @param $num Hierarchy number
     * @param $parent_id Parent ID
     */
    function apply_hierarchy($menuItem, $num, $parent_id)
    {
        // echo "apply_hierarchy: ".json_encode($menuItem)." - ".$num." - ".$parent_id."  <br><br>\n\n";
        $menu = Menu::find($menuItem['id']);
        $menu->parent = $parent_id;
        $menu->hierarchy = $num;
        $menu->save();
        
        // apply hierarchy to children if exists
        if(isset($menuItem['children'])) {
            for($i = 0; $i < count($menuItem['children']); $i++) {
                $this->apply_hierarchy($menuItem['children'][$i], $i + 1, $menuItem['id']);
            }
        }
    }
}
