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
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Illuminate\Support\Str;

use App\Models\User;

class UsersController extends Controller
{
	public $show_action = false;

	/**
	 * Display a listing of the Users.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Users');

		if(Module::hasAccess($module->id)) {
			return View('crm.users.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => Module::getListingColumns('Users'),
				'module' => $module
			]);
		} else {
            return redirect(config('crmadmin.adminRoute')."/");
        }
	}

	/**
	 * Display the specified user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Users", "view")) {
			$user = User::findOrFail($id);
			if(isset($user->id)) {
				if($user['type'] == "Employee") {
					return redirect(config('crmadmin.adminRoute') . '/employees/'.$user->id);
				} else if($user['type'] == "Client") {
					return redirect(config('crmadmin.adminRoute') . '/clients/'.$user->id);
				}
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("user"),
				]);
			}
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
		$module = Module::get('Users');
		$listing_cols = Module::getListingColumns('Users');

		$values = DB::table('users')->select($listing_cols)->whereNull('deleted_at');
		$out = \DataTables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Users');

		for($i=0; $i < count($data->data); $i++) {
            $data->data[$i] =(array)$data->data[$i];
			for ($j=0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = $data->data[$i][$col];
				if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$col]);
				}
				if($col == $module->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('crmadmin.adminRoute') . '/users/'.$data->data[$i][$listing_cols[0]]).'">'.$data->data[$i][$col].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
		}
		$out->setData($data);
		return $out;
	}
}
