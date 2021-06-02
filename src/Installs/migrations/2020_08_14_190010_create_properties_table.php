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

class CreatePropertiesTable extends Migration
{
    /**
     * Migration generate Module Table Schema by CrmAdmin
     *
     * @return void
     */
    public function up()
    {
        Module::generate("Properties", 'properties', 'name', 'fa-cogs', [
            [
                "colname" => "name",
                "label" => "Name",
                "field_type" => "Name",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 256,
                "required" => true,
                "listing_col" => true
            ], [
                "colname" => "description",
                "label" => "Mô tả",
                "field_type" => "String",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 255,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "value",
                "label" => "Value",
                "field_type" => "Taginput",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 256,
                "required" => false,
                "listing_col" => true,
                "popup_vals" => "null",
            ], [
                "colname" => "unit",
                "label" => "Đơ vị",
                "field_type" => "String",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 20,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "type_data",
                "label" => "Loại dữ liệu",
                "field_type" => "Radio",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 256,
                "required" => false,
                "listing_col" => true,
                "popup_vals" => ["Integer","String","Select","Text"],
            ], [
                "colname" => "filter",
                "label" => "Lọc",
                "field_type" => "Checkbox",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 0,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "show_colum",
                "label" => "Hiển thị cột",
                "field_type" => "Checkbox",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 0,
                "required" => false,
                "listing_col" => true
            ], [
                "colname" => "user_id",
                "label" => "User create",
                "field_type" => "Dropdown",
                "unique" => false,
                "defaultvalue" => "",
                "minlength" => 0,
                "maxlength" => 0,
                "required" => false,
                "listing_col" => true,
                "popup_vals" => "@users",
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
        if(Schema::hasTable('properties')) {
            Schema::drop('properties');
        }
    }
}
