<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth;
use App\Http\Controllers\CRM;

/* ================== Access Uploaded Files ================== */
Route::get('files/{hash}/{name}', [CRM\UploadsController::class, 'get_file']);

/*
|--------------------------------------------------------------------------
| Admin Application Routes
|--------------------------------------------------------------------------
*/

$as = "";
$as = config('crmadmin.adminRoute').'.';

// Authentication Routes...
Route::get('login', [Auth\LoginController:: class, 'showLoginForm'])->name('login');
Route::post('login', [Auth\LoginController::class, 'login']);
Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');

Route::get('register', [Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [Auth\RegisterController::class, 'create']);

Route::group(['as' => $as, 'middleware' => ['auth', 'permission:ADMIN_PANEL']], function () {

	/* ================== Dashboard ================== */

	Route::get(config('crmadmin.adminRoute'), [CRM\DashboardController::class, 'index']);
	Route::get(config('crmadmin.adminRoute'). '/dashboard', [CRM\DashboardController::class, 'index']);

	/* ================== Users ================== */
	Route::resource(config('crmadmin.adminRoute') . '/users', \CRM\UsersController::class);
	Route::get(config('crmadmin.adminRoute') . '/user_dt_ajax', [CRM\UsersController::class, 'dtajax']);

	/* ================== Uploads ================== */
	Route::resource(config('crmadmin.adminRoute') . '/uploads', \CRM\UploadsController::class);
	Route::post(config('crmadmin.adminRoute') . '/upload_files', [CRM\UploadsController::class, 'upload_files']);
    Route::get(config('crmadmin.adminRoute') . '/uploaded_files', [CRM\UploadsController::class, 'uploaded_files']);
    Route::get(config('crmadmin.adminRoute') . '/uploaded_files/{folder_end}', [CRM\UploadsController::class, 'uploaded_files'])->where(['folder_end' => '[a-zA-z0-9\-]+']);
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_caption', [CRM\UploadsController::class, 'update_caption']);
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_filename', [CRM\UploadsController::class, 'update_filename']);
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_public', [CRM\UploadsController::class, 'update_public']);
	Route::post(config('crmadmin.adminRoute') . '/uploads_delete_file', [CRM\UploadsController::class, 'delete_file']);

	/* ================== Roles ================== */
	Route::resource(config('crmadmin.adminRoute') . '/roles', \CRM\RolesController::class);
	Route::get(config('crmadmin.adminRoute') . '/role_dt_ajax', [CRM\RolesController::class, 'dtajax']);
	Route::post(config('crmadmin.adminRoute') . '/save_module_role_permissions/{id}', [CRM\RolesController::class, 'save_module_role_permissions']);

	/* ================== Permissions ================== */
	Route::resource(config('crmadmin.adminRoute') . '/permissions', \CRM\PermissionsController::class);
	Route::get(config('crmadmin.adminRoute') . '/permission_dt_ajax', [CRM\PermissionsController::class, 'dtajax']);
	Route::post(config('crmadmin.adminRoute') . '/save_permissions/{id}', [CRM\PermissionsController::class, 'save_permissions']);

	/* ================== Departments ================== */
	Route::resource(config('crmadmin.adminRoute') . '/departments', \CRM\DepartmentsController::class);
	Route::get(config('crmadmin.adminRoute') . '/department_dt_ajax', [CRM\DepartmentsController::class, 'dtajax']);

	/* ================== Employees ================== */
	Route::resource(config('crmadmin.adminRoute') . '/employees', \CRM\EmployeesController::class);
	Route::get(config('crmadmin.adminRoute') . '/employee_dt_ajax', [CRM\EmployeesController::class, 'dtajax']);
	Route::post(config('crmadmin.adminRoute') . '/change_password/{id}', [CRM\EmployeesController::class, 'change_password']);

	/* ================== Organizations ================== */
	Route::resource(config('crmadmin.adminRoute') . '/organizations', \CRM\OrganizationController::class);
    Route::get(config('crmadmin.adminRoute') . '/organization_dt_ajax', [CRM\OrganizationController::class, 'dtajax']);
    Route::post(config('crmadmin.adminRoute') . '/organization_field_switch', [CRM\OrganizationController::class, 'dtswitch']);
    Route::post(config('crmadmin.adminRoute') . '/organization_field_slider_switch', [CRM\OrganizationController::class, 'dtSlideSwitch']);

    /* ================== Languages ================== */
    Route::resource(config('crmadmin.adminRoute') . '/languages', \CRM\LanguageController::class);
    Route::get(config('crmadmin.adminRoute') . '/language_dt_ajax', [CRM\LanguageController::class, 'dtajax']);

	/* ================== Backups ================== */
	Route::resource(config('crmadmin.adminRoute') . '/backups', \CRM\BackupsController::class);
	Route::get(config('crmadmin.adminRoute') . '/backup_dt_ajax', [CRM\BackupsController::class, 'dtajax']);
	Route::post(config('crmadmin.adminRoute') . '/create_backup_ajax', [CRM\BackupsController::class, 'create_backup_ajax']);
    Route::get(config('crmadmin.adminRoute') . '/downloadBackup/{id}', [CRM\BackupsController::class, 'downloadBackup']);

    /* ================== Categories ================== */
    Route::resource(config('crmadmin.adminRoute') . '/categories', \CRM\CategoryController::class);
    Route::get(config('crmadmin.adminRoute') . '/category_dt_ajax', [CRM\CategoryController::class, 'dtajax']);
    Route::post(config('crmadmin.adminRoute') . '/category_field_switch', [CRM\CategoryController::class, 'dtswitch']);
    Route::post(config('crmadmin.adminRoute') .'/ca_menus/update_hierarchy', [CRM\CategoryController::class, 'update_hierarchy']);
    Route::post(config('crmadmin.adminRoute') . '/categories/add', [CRM\CategoryController::class, 'update_status']);

    /* ================== Properties ================== */
    Route::resource(config('crmadmin.adminRoute') . '/properties', \CRM\PropertyController::class);
    Route::get(config('crmadmin.adminRoute') . '/property_dt_ajax', [CRM\PropertyController::class, 'dtajax']);
    Route::post(config('crmadmin.adminRoute') . '/property_field_switch', [CRM\PropertyController::class, 'dtswitch']);


});