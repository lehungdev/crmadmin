<?php
/**
 * Config generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

return [
    
	/*
    |--------------------------------------------------------------------------
    | General Configuration
    |--------------------------------------------------------------------------
    */
    
	'adminRoute' => 'admin',
    
    /*
    |--------------------------------------------------------------------------
    | Uploads Configuration
    |--------------------------------------------------------------------------
    |
    | private_uploads: Uploaded file remains private and can be seen by respective owners + Super Admin only
    | default_public: Will make default uploads public / private
	| allow_filename_change: allows user to modify filenames after upload. Changes will be only in Database not on actual files.
    | 
    */
    'uploads' => [
        'private_uploads' => false,
        'default_public' => false,
        'allow_filename_change' => true
    ],
];