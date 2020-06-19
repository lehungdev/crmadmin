<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Lehungdev\Crmadmin\Helpers\LAHelper;
use Shanmuga\LaravelEntrust\LaravelEntrustFacade as LaravelEntrust;
use Illuminate\Support\Str;

use App\Permission;
use App\Role;

class PermissionsController extends Controller
{
	public $show_action = true;

	/**
	 * Display a listing of the Permissions.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Permissions');

		if(Module::hasAccess($module->id)) {
			return View('la.permissions.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => Module::getListingColumns('Permissions'),
				'module' => $module
			]);
		} else {
            return redirect(config('crmadmin.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new permission.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created permission in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Permissions", "create")) {

			$rules = Module::validateRules("Permissions", $request);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

			$insert_id = Module::insert("Permissions", $request);

			return redirect()->route(config('crmadmin.adminRoute') . '.permissions.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified permission.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Permissions", "view")) {

			$permission = Permission::find($id);
			if(isset($permission->id)) {
				$module = Module::get('Permissions');
				$module->row = $permission;

				$roles = Role::all();

				return view('la.permissions.show', [
					'module' => $module,
					'view_col' => $module->view_col,
					'no_header' => true,
					'no_padding' => "no-padding",
					'roles' => $roles
				])->with('permission', $permission);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("permission"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified permission.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Permissions", "edit")) {
			$permission = Permission::find($id);
			if(isset($permission->id)) {
				$module = Module::get('Permissions');

				$module->row = $permission;

				return view('la.permissions.edit', [
					'module' => $module,
					'view_col' => $module->view_col,
				])->with('permission', $permission);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("permission"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified permission in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Permissions", "edit")) {

			$rules = Module::validateRules("Permissions", $request, true);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}

			$insert_id = Module::updateRow("Permissions", $request, $id);

			return redirect()->route(config('crmadmin.adminRoute') . '.permissions.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified permission from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Permissions", "delete")) {
			Permission::find($id)->delete();

			// Redirecting to index() method
			return redirect()->route(config('crmadmin.adminRoute') . '.permissions.index');
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax(Request $request)
	{
		$module = Module::get('Permissions');
		$listing_cols = Module::getListingColumns('Permissions');

		$values = DB::table('permissions')->select($listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Permissions');

		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($listing_cols); $j++) {
				$col = $listing_cols[$j];
				if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $module->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('crmadmin.adminRoute') . '/permissions/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}

			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Permissions", "edit")) {
					$output .= '<a href="'.url(config('crmadmin.adminRoute') . '/permissions/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}

				if(Module::hasAccess("Permissions", "delete")) {
					$output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.permissions.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}

	/**
	 * Save the  permissions for role in permission view.
	 *
	 * @param  int  $id
	 * @return Redirect to permisssions page
	 */
	public function save_permissions(Request $request, $id)
	{
		if(LaravelEntrust::hasRole('SUPER_ADMIN')) {
			$permission = Permission::find($id);
			$module = Module::get('Permissions');
			$module->row = $permission;
			$roles = Role::all();

			foreach ($roles as $role) {
				$permi_role_id = 'permi_role_'.$role->id;
				$permission_set = $request->$permi_role_id;
				if(isset($permission_set)) {
					$query = DB::table('permission_role')->where('permission_id', $id)->where('role_id', $role->id);
					if($query->count() == 0) {
						DB::insert('insert into permission_role (permission_id, role_id) values (?, ?)', [$id, $role->id]);
					}
				} else {
					$query = DB::table('permission_role')->where('permission_id', $id)->where('role_id', $role->id);
					if($query->count() > 0) {
						DB::delete('delete from permission_role where permission_id = "'.$id.'" AND role_id = "'.$role->id.'" ');
					}
				}
			}
			return redirect(config('crmadmin.adminRoute') . '/permissions/'.$id."#tab-access");
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}
}
