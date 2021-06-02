<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

use Illuminate\Database\Seeder;

use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Lehungdev\Crmadmin\Models\ModuleFieldTypes;
use Lehungdev\Crmadmin\Models\Menu;
use Lehungdev\Crmadmin\Models\LAConfigs;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;
use App\Models\Language;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		/* ================ CrmAdmin Seeder Code ================ */

		// Generating Module Menus
		$modules = Module::all();
		$teamMenu = Menu::create([
			"name" => "Team",
			"url" => "#",
			"icon" => "fa-group",
			"type" => 'custom',
			"parent" => 0,
			"hierarchy" => 1
		]);
		foreach ($modules as $module) {
			$parent = 0;
			if($module->name != "Backups") {
				if(in_array($module->name, ["Users", "Departments", "Employees", "Roles", "Permissions"])) {
					$parent = $teamMenu->id;
				}
				Menu::create([
					"name" => $module->name,
					"url" => $module->name_db,
					"icon" => $module->fa_icon,
					"type" => 'module',
					"parent" => $parent
				]);
			}
		}

		// Create Administration Department
	   	$dept = new Department;
		$dept->name = "Administration";
		$dept->tags = "[]";
		$dept->color = "#000";
		$dept->save();

		// Create Super Admin Role
		$role = new Role;
		$role->name = "SUPER_ADMIN";
		$role->display_name = "Super Admin";
		$role->description = "Full Access Role";
		$role->parent = 1;
		$role->dept = $dept->id;
		$role->save();

		// Set Full Access For Super Admin Role
		foreach ($modules as $module) {
			Module::setDefaultRoleAccess($module->id, $role->id, "full");
		}

		// Create Admin Panel Permission
		$perm = new Permission;
		$perm->name = "ADMIN_PANEL";
		$perm->display_name = "Admin Panel";
		$perm->description = "Admin Panel Permission";
		$perm->save();

		$role->attachPermission($perm);

        // Create Language Default
        $language = new Language;
		$language->name = "Tiếng Việt";
		$language->image = 0;
		$language->locale = "vi";
		$language->save();

        $language = new Language;
		$language->name = "English";
		$language->image = 0;
		$language->locale = "en";
        $language->save();

		// Generate CrmAdmin Default Configurations

		$laconfig = new LAConfigs;
		$laconfig->key = "sitename";
		$laconfig->value = "CrmAdmin 2.0";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "sitename_part1";
		$laconfig->value = "Crm";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "sitename_part2";
		$laconfig->value = "Admin 2.0";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "sitename_short";
		$laconfig->value = "LA";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "site_description";
		$laconfig->value = "CrmAdmin is a open-source Crm Admin Panel for quick-start Admin based applications and boilerplate for CRM\ or CMS systems.";
		$laconfig->save();

		// Display Configurations

		$laconfig = new LAConfigs;
		$laconfig->key = "sidebar_search";
		$laconfig->value = "1";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "show_messages";
		$laconfig->value = "1";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "show_notifications";
		$laconfig->value = "1";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "show_tasks";
		$laconfig->value = "1";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "show_rightsidebar";
		$laconfig->value = "1";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "skin";
		$laconfig->value = "skin-green";
		$laconfig->save();

		$laconfig = new LAConfigs;
		$laconfig->key = "layout";
		$laconfig->value = "fixed";
		$laconfig->save();

		// Admin Configurations

		$laconfig = new LAConfigs;
		$laconfig->key = "default_email";
		$laconfig->value = "lehung.hut@gmail.com";
		$laconfig->save();

		$modules = Module::all();
		foreach ($modules as $module) {
			$module->is_gen=true;
			$module->save();
		}

		/* ================ Call Other Seeders ================ */

	}
}