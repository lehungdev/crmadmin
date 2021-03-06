<?php

/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace Lehungdev\Crmadmin;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFieldTypes;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Lehungdev\Crmadmin\Helpers\LAHelper;
use Lehungdev\Crmadmin\Models\Menu;
use Illuminate\Support\Str;
use File;

/**
 * Class CodeGenerator
 * @package Lehungdev\Crmadmin
 *
 * This class performs the Code Generation for Controller, Model, CRUDs Views, Routes, Menu and Migrations.
 * This also generates the naming config which contains names for controllers, tables and everything required
 * to generate CRUDs.
 */
class CodeGenerator
{
    /**
     * Generate Controller file
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function createController($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs';

        LAHelper::log("info", "Creating controller...", $comm);
        $md = file_get_contents($templateDirectory . "/controller.stub");

        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__model_name__", $config->modelName, $md);
        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__view_column__", $config->module->view_col, $md);

        // Listing columns
        $listing_cols = "";
        $listing_cols_dropdown = "";
        foreach ($config->module->fields as $field) {
            $listing_cols .= "'" . $field['colname'] . "', ";
            if ($field['field_type'] == 7  and Str::startsWith($field['popup_vals'], '@')) {
                $listing_cols_dropdown .= "'" . str_replace('_id', '', $field['colname']) . "', ";
            }
        }
        $listing_cols = trim($listing_cols, ", ");
        $listing_cols_dropdown = trim($listing_cols_dropdown, ", ");

        $md = str_replace("__listing_cols__", $listing_cols, $md);
        $md = str_replace("__listing_cols_dropdown__", $listing_cols_dropdown, $md);
        $md = str_replace("__view_folder__", $config->dbTableName, $md);
        $md = str_replace("__route_resource__", $config->dbTableName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);

        file_put_contents(base_path('app/Http/Controllers/CRM/' . $config->controllerName . ".php"), $md);
    }

    /**
     * Generate Controller file
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function createControllerApi($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs/api';

        LAHelper::log("info", "Creating controller api...", $comm);
        // Create Folder
        @mkdir(base_path("app/Http/Controllers/Api/" . $config->moduleName), 0777, true);

        $md = file_get_contents($templateDirectory . "/controller_api.stub");

        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__model_name__", $config->modelName, $md);
        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__view_column__", $config->module->view_col, $md);

        // Listing columns
        $listing_cols = "";
        $listing_cols_dropdown = "";
        foreach ($config->module->fields as $field) {
            $listing_cols .= "'" . $field['colname'] . "', ";
            if ($field['field_type'] == 7  and Str::startsWith($field['popup_vals'], '@')) {
                $listing_cols_dropdown .= "'" . str_replace('_id', '', $field['colname']) . "', ";
            }
        }
        $listing_cols = trim($listing_cols, ", ");
        $listing_cols_dropdown = trim($listing_cols_dropdown, ", ");

        $md = str_replace("__listing_cols__", $listing_cols, $md);
        $md = str_replace("__listing_cols_dropdown__", $listing_cols_dropdown, $md);
        $md = str_replace("__view_folder__", $config->dbTableName, $md);
        $md = str_replace("__route_resource__", $config->dbTableName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);

        file_put_contents(base_path('app/Http/Controllers/Api/' . $config->moduleName . '/' . $config->modelName . "ApiController.php"), $md);
    }


    /**
     * Generate Controller file
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function createControllerTransformer($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs/api';

        LAHelper::log("info", "Creating controller...", $comm);

        $md = file_get_contents($templateDirectory . "/controller_transformer.stub");

        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__model_name__", $config->modelName, $md);
        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__view_column__", $config->module->view_col, $md);

        // Listing columns
        $listing_cols = "";
        $listing_cols_dropdown = "";
        $use_model_dropdown = "";
        $use_model_dropdown_children = "";
        $use_model_belongsTo = "";

        foreach ($config->module->fields as $field) {

            $listing_cols .= "'" . $field['colname'] . "', ";
            if ($field['field_type'] == 7  and Str::startsWith($field['popup_vals'], '@')) {
                $use_model_belongsTo_parent = "";
                $popup_vals = Str::ucfirst(Str::singular(str_replace('@', '', $field['popup_vals'])));
                $listing_cols_dropdown .= "'" . Str::lower($popup_vals) . "', ";


                if (Str::ucfirst(Str::singular(str_replace('_id', '', $field['colname']))) !== $config->modelName) {
                    $use_model_dropdown .= "\nuse App\\Http\\Controllers\\Api\\" . Str::plural($popup_vals) . "\\" . $popup_vals . "Transformer;";
                }

                $use_model_belongsTo .= "\n\tpublic function include" . $popup_vals . "(" . $config->modelName . " $" . $config->dbTableName . ")\n\t{";
                $use_model_belongsTo .= "\n\t\tif (!is_null($" . $config->dbTableName . "->" . Str::lower($popup_vals) . ")) {";
                $use_model_belongsTo .= "\n\t\t\treturn \$this->item($" . $config->dbTableName . "->" . Str::lower($popup_vals) . ", new " . $popup_vals . "Transformer);";
                $use_model_belongsTo .= "\n\t\t}\n";
                $use_model_belongsTo .= "\n\t}\n";

                // ============================ Get parent ============================
                $path_parent = base_path('app/Http/Controllers/Api/' . Str::ucfirst(Str::plural($popup_vals)) . '/' . Str::ucfirst(Str::singular($popup_vals)) . "Transformer.php");
                try {
                    if (File::exists($path_parent)) {
                        $md_parent = file_get_contents($path_parent);

                        //use_model_belongsTo_parent
                        $use_model_belongsTo_parent .= "\n\tpublic function include" . $config->modelName . "(" . Str::ucfirst(Str::singular($popup_vals)) . " $" . str_replace('@', '', $field['popup_vals']) . ")\n\t{";
                        $use_model_belongsTo_parent .= "\n\t\tif (!is_null($" . str_replace('@', '', $field['popup_vals']) . "->" . Str::lower($config->modelName) .")){";
                        $use_model_belongsTo_parent .= "\n\t\t\treturn \$this->collection($" . str_replace('@', '', $field['popup_vals']) . "->" . Str::lower($config->modelName) . ", new " . $config->modelName . "Transformer);";
                        $use_model_belongsTo_parent .= "\n\t\t}\n";

                        $use_model_belongsTo_parent .= "\n\t}\n\t//Add_belongsTo_parent";
                        if (!Str::contains($md_parent, "include" . $config->modelName)) {
                            $md_parent = str_replace("//Add_belongsTo_parent", $use_model_belongsTo_parent, $md_parent);
                            $md_parent = str_replace("//Add_path_parent", "use App\\Http\\Controllers\\Api\\" . Str::plural($config->modelName) . "\\" . $config->modelName . "Transformer;\n//Add_path_parent", $md_parent);

                            file_put_contents($path_parent, $md_parent);
                        };
                    }
                } catch (Exception $e) {

                }
            }
        }

        // ============================ Get number of Module fields ============================
        $modulefields = ModuleFields::where('popup_vals', '@'.$config->dbTableName)->get();
        $use_model_belongsTo_children = '';
        if(count($modulefields) > 0){
            foreach($modulefields as $field_value ){
                $module = Module::find($field_value->module);
                $use_model_dropdown_children .= "\nuse App\\Http\\Controllers\\Api\\" . Str::ucfirst(Str::plural($module->model)) . "\\" . $module->model . "Transformer;";
                $listing_cols_dropdown .= "'" . Str::lower($module->model) . "', ";
                try {
                        //use_model_belongsTo_children
                        $use_model_belongsTo_children .= "\n\tpublic function include" . $module->model . "(" . $config->modelName . " $" . $config->dbTableName . ")\n\t{";
                        $use_model_belongsTo_children .= "\n\t\tif (!is_null($" . $config->dbTableName . "->" . Str::lower($module->model) .")){";
                        $use_model_belongsTo_children .= "\n\t\t\treturn \$this->collection($" . $config->dbTableName . "->" . Str::lower($module->model) . ", new " .$module->model . "Transformer);";
                        $use_model_belongsTo_children .= "\n\t\t}\n";
                        $use_model_belongsTo_children .= "\n\t}";
                } catch (Exception $e) {

                }
            }
        }

        $listing_cols = trim($listing_cols, ", ");
        $listing_cols_dropdown = trim($listing_cols_dropdown, ", ");

        $md = str_replace("__listing_cols__", $listing_cols, $md);
        $md = str_replace("__listing_cols_dropdown__", $listing_cols_dropdown, $md);
        // $md = str_replace("];//Add children", $dropdown_children, $md);
        $md = str_replace("__use_model_dropdown__", $use_model_dropdown, $md);
        $md = str_replace("__use_model_dropdown_children__", $use_model_dropdown_children, $md);
        $md = str_replace("__use_model_belongsTo__", $use_model_belongsTo, $md);
        $md = str_replace("__use_model_belongsTo_children__", $use_model_belongsTo_children, $md);
        $md = str_replace("__view_folder__", $config->dbTableName, $md);
        $md = str_replace("__route_resource__", $config->dbTableName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);

        file_put_contents(base_path('app/Http/Controllers/Api/' . $config->moduleName . '/' . $config->modelName . "Transformer.php"), $md);
    }

    /**
     * Generate Model file
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function createModel($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs';

        LAHelper::log("info", "Creating model...", $comm);
        $md = file_get_contents($templateDirectory . "/model.stub");

        $use_model_belongsTo = "";

        foreach ($config->module->fields as $field) {
            if ($field['field_type'] == 7 and Str::startsWith($field['popup_vals'], '@')) {
                $use_model_hasMany = "";
                $module = Module::where('name_db', str_replace('@', '', $field['popup_vals']))->first();
                if($module){
                    $use_model_belongsTo .= "\n\tpublic function " . Str::lower($module->model) . "()\n\t{";
                    $use_model_belongsTo .= "\n\t\t\treturn \$this->belongsTo(" . $module->model . "::class, '". $field['colname'] ."');";
                    $use_model_belongsTo .= "\n\t}\n";
                }


                // ============================ Get parent ============================
                $path_parent = base_path('app/Models/' . $module->model . ".php");
                try {
                    if (File::exists($path_parent)) {
                        $md_parent = file_get_contents($path_parent);

                        //use_model_hasMany
                        $use_model_hasMany .= "\n\tpublic function " . Str::lower($config->modelName) . "()\n\t{";
                        $use_model_hasMany .= "\n\t\t\treturn \$this->hasMany(" . $config->modelName . "::class, '".$field['colname']."');";
                        $use_model_hasMany .= "\n\t}\n\t//Add_hasMany";
                        if (!Str::contains($md_parent, "\n\t\t\treturn \$this->hasMany(" . $config->modelName . "::class, '".$field['colname']."');")) {
                            $md_parent = str_replace("//Add_hasMany", $use_model_hasMany, $md_parent);
                            file_put_contents($path_parent, $md_parent);
                        };
                    }
                } catch (Exception $e) {

                }
            }
        }

        // ============================ Get number of Module fields ============================
        $modulefields = ModuleFields::where('popup_vals', '@'.$config->dbTableName)->get();
        $use_model_belongsTo_children = '';
        if(count($modulefields) > 0){
            foreach($modulefields as $field_value ){
                $module = Module::find($field_value->module);
                try {
                        //use_model_belongsTo_children
                        $use_model_belongsTo_children .= "\n\tpublic function " . Str::lower($module->model) . "()\n\t{";
                        $use_model_belongsTo_children .= "\n\t\t\treturn \$this->hasMany(" . $module->model . "::class, '".$field_value->colname."');";
                        $use_model_belongsTo_children .= "\n\t}\n";
                } catch (Exception $e) {

                }
            }
        }

        $md = str_replace("__model_class_name__", $config->modelName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__use_model_belongsTo__", $use_model_belongsTo, $md);
        $md = str_replace("__use_model_belongsTo_children__", $use_model_belongsTo_children, $md);

        file_put_contents(base_path('app/Models/' . $config->modelName . ".php"), $md);
    }

    /**
     * Generate Views for CRUD
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function createViews($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs';

        LAHelper::log("info", "Creating views...", $comm);
        // Create Folder
        @mkdir(base_path("resources/views/crm/" . $config->dbTableName), 0777, true);

        // ============================ Listing / Index ============================
        $md = file_get_contents($templateDirectory . "/views/index.blade.stub");

        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);
        $md = str_replace("__singular_cap_var__", $config->singularCapitalVar, $md);
        $md = str_replace("__module_name_2__", $config->moduleName2, $md);

        // Listing columns
        $inputFields = "";
        foreach ($config->module->fields as $field) {
            $inputFields .= "\t\t\t\t\t@la_input($" . "module, '" . $field['colname'] . "')\n";
        }
        $inputFields = trim($inputFields);
        $md = str_replace("__input_fields__", $inputFields, $md);

        file_put_contents(base_path('resources/views/crm/' . $config->dbTableName . '/index.blade.php'), $md);

        // ============================ Edit ============================
        $md = file_get_contents($templateDirectory . "/views/edit.blade.stub");

        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);
        $md = str_replace("__singular_cap_var__", $config->singularCapitalVar, $md);
        $md = str_replace("__module_name_2__", $config->moduleName2, $md);

        // Listing columns
        $inputFields = "";
        foreach ($config->module->fields as $field) {
            $inputFields .= "\t\t\t\t\t@la_input($" . "module, '" . $field['colname'] . "')\n";
        }
        $inputFields = trim($inputFields);
        $md = str_replace("__input_fields__", $inputFields, $md);

        file_put_contents(base_path('resources/views/crm/' . $config->dbTableName . '/edit.blade.php'), $md);

        // ============================ Show ============================
        $md = file_get_contents($templateDirectory . "/views/show.blade.stub");

        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);
        $md = str_replace("__singular_cap_var__", $config->singularCapitalVar, $md);
        $md = str_replace("__module_name_2__", $config->moduleName2, $md);

        // Listing columns
        $displayFields = "";
        foreach ($config->module->fields as $field) {
            $displayFields .= "\t\t\t\t\t\t@la_display($" . "module, '" . $field['colname'] . "')\n";
        }
        $displayFields = trim($displayFields);
        $md = str_replace("__display_fields__", $displayFields, $md);

        file_put_contents(base_path('resources/views/crm/' . $config->dbTableName . '/show.blade.php'), $md);
    }

    /**
     * Append module controller routes to admin_routes.php
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function appendRoutes($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs';

        LAHelper::log("info", "Appending routes...", $comm);
        if (\Lehungdev\Crmadmin\Helpers\LAHelper::laravel_ver() != 5.3) {
            $routesFile = base_path('routes/admin_routes.php');
        } else {
            $routesFile = app_path('Http/admin_routes.php');
        }

        $contents = file_get_contents($routesFile);
        $contents = str_replace('});', '', $contents);
        file_put_contents($routesFile, $contents);

        $md = file_get_contents($templateDirectory . "/routes.stub");

        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);
        $md = str_replace("__singular_cap_var__", $config->singularCapitalVar, $md);
        file_put_contents($routesFile, $md, FILE_APPEND);
    }

    /**
     * Append module controller routes to api.php
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function appendRoutesApi($config, $comm = null)
    {

        $templateDirectory = __DIR__ . '/stubs';

        LAHelper::log("info", "Appending routes api...", $comm);
        if (\Lehungdev\Crmadmin\Helpers\LAHelper::laravel_ver() != 5.3) {
            $routesFile = base_path('routes/api.php');
        } else {
            $routesFile = app_path('Http/api.php');
        }

        $contents = file_get_contents($routesFile);
        $contents = str_replace('});//Add', '', $contents);
        file_put_contents($routesFile, $contents);

        $md = file_get_contents($templateDirectory . "/route_api.stub");

        $md = str_replace("__module_name__", $config->moduleName, $md);
        $md = str_replace("__module_model__", $config->modelName, $md);
        $md = str_replace("__controller_class_name__", $config->controllerName, $md);
        $md = str_replace("__db_table_name__", $config->dbTableName, $md);
        $md = str_replace("__singular_var__", $config->singularVar, $md);
        $md = str_replace("__singular_cap_var__", $config->singularCapitalVar, $md);
        file_put_contents($routesFile, $md, FILE_APPEND);
    }

    /**
     * Add Module to Menu
     *
     * @param $config config object storing the Module Names
     * @param null $comm command Object
     */
    public static function addMenu($config, $comm = null)
    {

        // $templateDirectory = __DIR__.'/stubs';

        LAHelper::log("info", "Appending Menu...", $comm);
        if (Menu::where("url", $config->dbTableName)->count() == 0) {
            Menu::create([
                "name" => $config->moduleName,
                "url" => $config->dbTableName,
                "icon" => "fa " . $config->fa_icon,
                "type" => 'module',
                "parent" => 0
            ]);
        }

        // Old Method to add Menu
        // $menu = '<li><a href="{{ url(config("crmadmin.adminRoute") . '."'".'/'.$config->dbTableName."'".') }}"><i class="fa fa-cube"></i> <span>'.$config->moduleName.'</span></a></li>'."\n".'            <!-- LAMenus -->';
        // $md = file_get_contents(base_path('resources/views/crm/layouts/partials/sidebar.blade.php'));
        // $md = str_replace("<!-- LAMenus -->", $menu, $md);
        // file_put_contents(base_path('resources/views/crm/layouts/partials/sidebar.blade.php'), $md);
    }

    /**
     * Generate migration file
     *
     * CodeGenerator::generateMigration($table, $generateFromTable);
     *
     * @param $table table name
     * @param bool $generate true then create file from module info from DB
     * @param null $comm command Object
     * @throws Exception
     */
    public static function generateMigration($table, $generate = false, $comm = null)
    {
        $filesystem = new Filesystem();

        if (Str::startsWith($table, "create_")) {
            $tname = str_replace("create_", "", $table);
            $table = str_replace("_table", "", $tname);
        }

        $modelName = Str::ucfirst(Str::plural($table));
        $tableP = Str::plural(strtolower($table));
        $tableS = Str::plural(strtolower($table));
        $migrationName = 'create_' . $tableP . '_table';
        $migrationFileName = date("Y_m_d_His_") . $migrationName . ".php";
        $migrationClassName = Str::ucfirst(Str::camel($migrationName));
        $dbTableName = $tableP;
        $moduleName = Str::ucfirst(Str::plural($table));

        LAHelper::log("info", "Model:\t   " . $modelName, $comm);
        LAHelper::log("info", "Module:\t   " . $moduleName, $comm);
        LAHelper::log("info", "Table:\t   " . $dbTableName, $comm);
        LAHelper::log("info", "Migration: " . $migrationName . "\n", $comm);

        // Reverse migration generation from table
        $generateData = "";
        $viewColumnName = "view_column_name e.g. name";

        // fa_icon
        $faIcon = "fa-cube";

        if ($generate) {
            // check if table, module and module fields exists
            $module = Module::get($moduleName);
            if (isset($module)) {
                LAHelper::log("info", "Module exists :\t   " . $moduleName, $comm);

                $viewColumnName = $module->view_col;
                $faIcon = $module->fa_icon;
                $ftypes = ModuleFieldTypes::getFTypes2();
                foreach ($module->fields as $field) {
                    $ftype = $ftypes[$field['field_type']];
                    $unique = "false";
                    if ($field['unique']) {
                        $unique = "true";
                    }
                    $dvalue = "";
                    if ($field['defaultvalue'] != "") {
                        if (Str::startsWith($field['defaultvalue'], "[")) {
                            $dvalue = $field['defaultvalue'];
                        } else {
                            $dvalue = '"' . $field['defaultvalue'] . '"';
                        }
                    } else {
                        $dvalue = '""';
                    }
                    $minlength = $field['minlength'];
                    $maxlength = $field['maxlength'];
                    $required = "false";
                    if ($field['required']) {
                        $required = "true";
                    }
                    $listing_col = "false";
                    if ($field['listing_col']) {
                        $listing_col = "true";
                    }
                    $values = "";
                    if ($field['popup_vals'] != "") {
                        if (Str::startsWith($field['popup_vals'], "[")) {
                            $values = $field['popup_vals'];
                        } else {
                            $values = '"' . $field['popup_vals'] . '"';
                        }
                    }
                    // $generateData .= '["'.$field['colname'].'", "'.$field['label'].'", "'.$ftype.'", '.$unique.', '.$dvalue.', '.$minlength.', '.$maxlength.', '.$required.''.$values.'],'."\n            ";
                    $generateData .= "[" .
                        "\n                \"colname\" => \"" . $field['colname'] . "\"," .
                        "\n                \"label\" => \"" . $field['label'] . "\"," .
                        "\n                \"field_type\" => \"" . $ftype . "\"," .
                        "\n                \"unique\" => " . $unique . "," .
                        "\n                \"defaultvalue\" => " . $dvalue . "," .
                        "\n                \"minlength\" => " . $minlength . "," .
                        "\n                \"maxlength\" => " . $maxlength . "," .
                        "\n                \"required\" => " . $required . ",";

                    if ($values != "") {
                        $generateData .= "\n                \"listing_col\" => " . $listing_col . ",";
                        $generateData .= "\n                \"popup_vals\" => " . $values . ",";
                    } else {
                        $generateData .= "\n                \"listing_col\" => " . $listing_col . "";
                    }
                    $generateData .= "\n            ], ";
                }
                $generateData = trim($generateData, ", ");

                // Find existing migration file
                $mfiles = scandir(base_path('database/migrations/'));
                // print_r($mfiles);
                $fileExists = false;
                $fileExistName = "";
                foreach ($mfiles as $mfile) {
                    if (str_contains($mfile, $migrationName)) {
                        $fileExists = true;
                        $fileExistName = $mfile;
                    }
                }
                if ($fileExists) {
                    LAHelper::log("info", "Replacing old migration file: " . $fileExistName, $comm);
                    $migrationFileName = $fileExistName;
                } else {
                    // If migration not exists in migrations table
                    if (\DB::table('migrations')->where('migration', 'like', '%' . $migrationName . '%')->count() == 0) {
                        \DB::table('migrations')->insert([
                            'migration' => str_replace(".php", "", $migrationFileName),
                            'batch' => 1
                        ]);
                    }
                }
            } else {
                LAHelper::log("error", "Module " . $moduleName . " doesn't exists; Cannot generate !!!", $comm);
            }
        }

        $templateDirectory = __DIR__ . '/stubs';

        try {
            LAHelper::log("line", "Creating migration...", $comm);
            $migrationData = file_get_contents($templateDirectory . "/migration.stub");

            $migrationData = str_replace("__migration_class_name__", $migrationClassName, $migrationData);
            $migrationData = str_replace("__db_table_name__", $dbTableName, $migrationData);
            $migrationData = str_replace("__module_name__", $moduleName, $migrationData);
            $migrationData = str_replace("__model_name__", $modelName, $migrationData);
            $migrationData = str_replace("__view_column__", $viewColumnName, $migrationData);
            $migrationData = str_replace("__fa_icon__", $faIcon, $migrationData);
            $migrationData = str_replace("__generated__", $generateData, $migrationData);

            file_put_contents(base_path('database/migrations/' . $migrationFileName), $migrationData);

            // Load newly generated migration into environment. Needs in testing mode.
            require_once base_path('database/migrations/' . $migrationFileName);
        } catch (Exception $e) {
            throw new Exception("Unable to generate migration for " . $table . " : " . $e->getMessage(), 1);
        }
        LAHelper::log("info", "Migration done: " . $migrationFileName . "\n", $comm);
    }

    /**
     * Generate naming configuration for passed module required to generate
     * CRUDs, Model, Controller and Migration files
     *
     * $config = CodeGenerator::generateConfig($module_name);
     *
     * @param $module Module table in lowercase
     * @param $icon Module icon - FontAwesome
     * @return object Config Object with different names of Module
     * @throws Exception When Migration for this Module is not done
     */
    public static function generateConfig($module, $icon)
    {
        $config = array();
        $config = (object)$config;

        if (Str::startsWith($module, "create_")) {
            $tname = str_replace("create_", "", $module);
            $module = str_replace("_table", "", $tname);
        }

        $config->modelName = Str::ucfirst(Str::singular($module));
        $tableP = Str::plural(strtolower($module));
        $tableS = Str::singular(strtolower($module));
        $config->dbTableName = $tableP;
        $config->fa_icon = $icon;
        $config->moduleName = Str::ucfirst(Str::plural($module));
        $config->moduleName2 = str_replace('_', ' ', Str::ucfirst(Str::plural($module)));
        $config->controllerName = Str::ucfirst(Str::singular($module)) . "Controller";
        $config->singularVar = strtolower(Str::singular($module));
        $config->singularCapitalVar = str_replace('_', ' ', Str::ucfirst(Str::singular($module)));

        $module = Module::get($config->moduleName);
        if (!isset($module->id)) {
            throw new Exception("Please run 'php artisan migrate' for 'create_" . $config->dbTableName . "_table' in order to create CRUD.\nOr check if any problem in Module Name '" . $config->moduleName . "'.", 1);
            return;
        }
        $config->module = $module;

        return $config;
    }
}
