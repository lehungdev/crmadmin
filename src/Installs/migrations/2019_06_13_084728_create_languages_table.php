<?php
/**
 * Migration generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Lehungdev\Crmadmin\Models\Module;

class CreateLanguagesTable extends Migration
{
    /**
     * Migration generate Module Table Schema by CrmAdmin
     *
     * @return void
     */
    public function up()
    {
        Module::generate("Languages", 'languages', 'name', 'fa-language', [
            [
                "colname" => "name",
                "label" => "Name",
                "field_type" => "Name",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 2,
                "maxlength" => 120,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "image",
                "label" => "Hình ảnh",
                "field_type" => "Image",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 0,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "locale",
                "label" => "Locale",
                "field_type" => "String",
                "unique" => true,
                "defaultvalue" => "",
                "minlength" => 1,
                "maxlength" => 250,
                "required" => true,
                "listing_col" => true
            ]
        ]);
        
        /*
        Module::generate("Module_Name", "Table_Name", "view_column_name" "Fields_Array");

        Field Format:
        [
            "colname" => "name",
            "label" => "Name",
            "field_type" => "Name",
            "unique" => false,
            "defaultvalue" => "John Doe",
            "minlength" => 5,
            "maxlength" => 100,
            "required" => true,
            "listing_col" => true,
            "popup_vals" => ["Employee", "Client"]
        ]
        # Format Details: Check http://rellifetech.com/docs/migrations_cruds#schema-ui-types
        
        colname: Database column name. lowercase, words concatenated by underscore (_)
        label: Label of Column e.g. Name, Cost, Is Public
        field_type: It defines type of Column in more General way.
        unique: Whether the column has unique values. Value in true / false
        defaultvalue: Default value for column.
        minlength: Minimum Length of value in integer.
        maxlength: Maximum Length of value in integer.
        required: Is this mandatory field in Add / Edit forms. Value in true / false
        listing_col: Is allowed to show in index page datatable.
        popup_vals: These are values for MultiSelect, TagInput and Radio Columns. Either connecting @tables or to list []
        */
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('languages')) {
            Schema::drop('languages');
        }
    }
}
