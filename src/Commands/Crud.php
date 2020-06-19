<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace Lehungdev\Crmadmin\Commands;

use Config;
use Artisan;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\CodeGenerator;

/**
 * Class Crud
 * @package Lehungdev\Crmadmin\Commands
 *
 * Command that generates CRUD's for a Module. Takes Module name as input.
 */
class Crud extends Command
{
    // ================ CRUD Config ================
    var $module = null;
    var $controllerName = "";
    var $modelName = "";
    var $moduleName = "";
    var $dbTableName = "";
    var $singularVar = "";
    var $singularCapitalVar = "";
    
    // The command signature.
    protected $signature = 'la:crud {module}';
    
    // The command description.
    protected $description = 'Generate CRUD\'s, Controller, Model, Routes and Menu for given Module.';
    
    /**
     * Generate a CRUD files including Controller, Model, Views, Routes and Menu
     *
     * @throws Exception
     */
    public function handle()
    {
        $module = $this->argument('module');
        
        try {
            
            $config = CodeGenerator::generateConfig($module, "fa-cube");
            
            CodeGenerator::createController($config, $this);
            CodeGenerator::createModel($config, $this);
            CodeGenerator::createViews($config, $this);
            CodeGenerator::appendRoutes($config, $this);
            CodeGenerator::addMenu($config, $this);
            
        } catch(Exception $e) {
            $this->error("Crud::handle exception: " . $e);
            throw new Exception("Unable to generate migration for " . ($module) . " : " . $e->getMessage(), 1);
        }
        $this->info("\nCRUD successfully generated for " . ($module) . "\n");
    }
}
