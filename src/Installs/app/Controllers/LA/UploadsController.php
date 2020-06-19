<?php
/**
 * Controller genrated using IdeaAdmin
 * Help: lehung.hut@gmail.com
 */

namespace App\Http\Controllers\IdeaAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Input;
use Collective\Html\FormFacade as Form;

use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Helpers\LAHelper;
use Shanmuga\LaravelEntrust\LaravelEntrustFacade as LaravelEntrust;

use Auth;
use DB;
use File;
use Validator;
use Datatables;

use App\Models\Upload;

class UploadsController extends Controller
{
    public $show_action = true;


    public function __construct() {
        // for authentication (optional)
        $this->middleware('auth', ['except' => 'get_file']);

    }

    /**
     * Display a listing of the Uploads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;

        if($user_id == 1)
            $module = Module::get('Uploads');//->orderBy('id', 'desc')
        else
            $module = Module::get('Uploads')->where("user_id", $user_id);

        if(Module::hasAccess($module->id)) {
            return View('la.uploads.index', [
                'show_actions' => $this->show_action,
                'module' => $module
            ]);
        } else {
            return redirect(config('crmadmin.adminRoute')."/");
        }
    }

    /**
     * Get file
     *
     * @return \Illuminate\Http\Response
     */
    public function get_file($hash, $name)
    {
        //$user_id = Auth::user()->id;

        //if($user_id == 1)
        //$upload = Upload::where("hash", $hash)->first();
        //else
        //$upload = Upload::where("hash", $hash)->where("user_id", $user_id)->first();

        $upload = Upload::where("hash", $hash)->first();
        // Validate Upload Hash & Filename
        if(!isset($upload->id) || $upload->name != $name) {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access 1"
            ]);
        }

        if($upload->public == 1) {
            $upload->public = true;
        } else {
            $upload->public = false;
        }

        // Validate if Image is Public
        if(!$upload->public && !isset(Auth::user()->id)) {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access 2",
            ]);
        }

        if($upload->public || LaravelEntrust::hasRole('SUPER_ADMIN') || Auth::user()->id == $upload->user_id) {

            $path = $upload->path;

            if(!File::exists($path))
                abort(404);

            // Check if thumbnail
            $size = Input::get('s');

            if(isset($size)) {
                if(!is_numeric($size)) {
                    $size = 150;
                }
                $thumbpath = storage_path("thumbnails/".basename($upload->path)."-".$size."x".$size);

                if(File::exists($thumbpath)) {
                    $path = $thumbpath;
                } else {
                    // Create Thumbnail
                    LAHelper::createThumbnail($upload->path, $thumbpath, $size, $size, "transparent");
                    $path = $thumbpath;
                }
            }
            // Check if thumbnail
            $width = Input::get('w');
            if(!empty(Input::get('h')))
                $height = Input::get('h');
            else $height = $width;

            if(isset($width)) {
                if(!is_numeric($width)) {
                    $width = 150;
                    if(isset($height)) {
                        $height = $height;
                    } else {
                        $height = $width;
                    }
                }
                $thumbpath = storage_path("thumbnails/".basename($upload->path)."-".$width."x".$height);

                if(File::exists($thumbpath)) {
                    $path = $thumbpath;
                } else {
                    // Create Thumbnail
                    LAHelper::createThumbnail($upload->path, $thumbpath, $width, $height, "transparent");
                    $path = $thumbpath;
                }
            }

//			$proportion = Input::get('p');
//			if(!is_numeric($proportion)){
//				$proportion = explode(',',$proportion);
//				$proportion_w = $proportion[0];
//				$proportion_h = $proportion[1];
//
//			} else {
//				$proportion_w = 1;
//				$proportion_h = 1;
//			}


            $file = File::get($path);
            $type = File::mimeType($path);

            $download = Input::get('download');
            if(isset($download)) {
                return response()->download($path, $upload->name);
            } else {
                $response = FacadeResponse::make($file, 200);
                $response->header("Content-Type", $type);
            }

            return $response;
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access 3"
            ]);
        }
    }

    /**
     * Upload fiels via DropZone.js
     *
     * @return \Illuminate\Http\Response
     */
    public function upload_files() {

        if(Module::hasAccess("Uploads", "create")) {
            $input = Input::all();

            if(Input::hasFile('file'))
            {
                /*
                $rules = array(
                    'file' => 'mimes:jpg,jpeg,bmp,png,pdf|max:3000',
                );
                $validation = Validator::make($input, $rules);
                if ($validation->fails()) {
                    return response()->json($validation->errors()->first(), 400);
                }
                */
                $file = Input::file('file');

//				 print_r($file);

                $folder = storage_path('uploads');
                $folder1 = public_path() . '/uploads';
                $filename = $file->getClientOriginalName();

                $type_file3 = '.'.str_slug(substr( $filename,  strlen($filename) - 4, strlen($filename)));
                $type_file4 = '.'.str_slug(substr( $filename,  strlen($filename) - 5, strlen($filename)));
                $type_file5 = '.'.str_slug(substr( $filename,  strlen($filename) - 6, strlen($filename)));
                if( starts_with($type_file3, '.')) {
                    $name_file = str_slug(substr( $filename,  0, strlen($filename) - 4), '-');
                    $filename = $name_file.$type_file3;
                }
                else if(starts_with($type_file4, '.') ){
                    $name_file = str_slug(substr( $filename,  0, strlen($filename) - 5), '-');
                    $filename = $name_file.$type_file4;
                }
                else{
                    $name_file = str_slug(substr( $filename,  0, strlen($filename) - 6), '-');
                    $filename = $name_file.$type_file5;
                }
                $date_append = date("Y-m-d-His-");
                $file_move = Input::file('file');
                $upload_success = $file_move->move($folder1, $date_append.$filename);
                //	$upload_success1 = $file_move->move($folder, $date_append.$filename);
                $upload_success1 = copy($folder1.'/'.$date_append.$filename, $folder.'/'.$date_append.$filename);


                if( $upload_success ) {

                    // Get public preferences
                    // config("crmadmin.uploads.default_public")
                    $public = Input::get('public');
                    if(isset($public)) {
                        $public = true;
                    } else {
                        $public = false;
                    }

                    $upload = Upload::create([
                        "name" => $filename,
                        "path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
                        "path_name" => 'uploads/'.$date_append.$filename,
                        "extension" => pathinfo($filename, PATHINFO_EXTENSION),
                        "caption" => "",
                        "hash" => "",
                        "public" => $public,
                        "user_id" => Auth::user()->id
                    ]);
                    // apply unique random hash to file
                    while(true) {
                        $hash = strtolower(str_random(20));
                        if(!Upload::where("hash", $hash)->count()) {
                            $upload->hash = $hash;
                            break;
                        }
                    }
                    $upload->save();

                    return response()->json([
                        "status" => "success",
                        "upload" => $upload
                    ], 200);
                } else {
                    return response()->json([
                        "status" => "error"
                    ], 400);
                }
            } else {
                return response()->json('error: upload file not found.', 400);
            }
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }

    /**
     * Get all files from uploads folder
     *
     * @return \Illuminate\Http\Response
     */
    public function uploaded_files()
    {
        $user_id = Auth::user()->id;

        if(Module::hasAccess("Uploads", "view")) {
            $uploads = array();

            // print_r(Auth::user()->roles);
            if(LaravelEntrust::hasRole('SUPER_ADMIN')) {
                $uploads = Upload::orderBy('id', 'desc')->get();
            } else {
//				if(config('crmadmin.uploads.private_uploads')) {
                // Upload::where('user_id', 0)->first();
//					$uploads = Auth::user()->uploads;
//				} else {
                //$uploads = Upload::where("user_id", $user_id)->orderBy('id', 'desc');
//				}
                $uploads = Upload::orderBy('id', 'desc')->where("user_id", $user_id)->get();
            }
            $uploads2 = array();
            foreach ($uploads as $upload) {
                $u = (object) array();
                $u->id = $upload->id;
                $u->name = $upload->name;
                $u->extension = $upload->extension;
                $u->hash = $upload->hash;
                $u->public = $upload->public;
                $u->caption = $upload->caption;
                //$u->user = $upload->user->name;

                $uploads2[] = $u;
            }

            // $folder = storage_path('/uploads');
            // $files = array();
            // if(file_exists($folder)) {
            //     $filesArr = File::allFiles($folder);
            //     foreach ($filesArr as $file) {
            //         $files[] = $file->getfilename();
            //     }
            // }
            // return response()->json(['files' => $files]);
            return response()->json(['uploads' => $uploads2]);
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }

    /**
     * Update Uploads Caption
     *
     * @return \Illuminate\Http\Response
     */
    public function update_caption()
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = Input::get('file_id');
            $caption = Input::get('caption');

            $upload = Upload::find($file_id);
            if(isset($upload->id)) {
                if($upload->user_id == Auth::user()->id || LaravelEntrust::hasRole('SUPER_ADMIN')) {

                    // Update Caption
                    $upload->caption = $caption;
                    $upload->save();

                    return response()->json([
                        'status' => "success"
                    ]);

                } else {
                    return response()->json([
                        'status' => "failure",
                        'message' => "Upload not found"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => "failure",
                    'message' => "Upload not found"
                ]);
            }
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }

    /**
     * Update Uploads Filename
     *
     * @return \Illuminate\Http\Response
     */
    public function update_filename()
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = Input::get('file_id');
            $filename = Input::get('filename');


            $upload = Upload::find($file_id);
            if(isset($upload->id)) {
                if($upload->user_id == Auth::user()->id || LaravelEntrust::hasRole('SUPER_ADMIN')) {

                    // Update Caption
                    $upload->name = $filename;
                    $upload->save();

                    return response()->json([
                        'status' => "success"
                    ]);

                } else {
                    return response()->json([
                        'status' => "failure",
                        'message' => "Unauthorized Access 1"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => "failure",
                    'message' => "Upload not found"
                ]);
            }
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }

    /**
     * Update Uploads Public Visibility
     *
     * @return \Illuminate\Http\Response
     */
    public function update_public()
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = Input::get('file_id');
            $public = Input::get('public');
            if(isset($public)) {
                $public = true;
            } else {
                $public = false;
            }

            $upload = Upload::find($file_id);
            if(isset($upload->id)) {
                if($upload->user_id == Auth::user()->id || LaravelEntrust::hasRole('SUPER_ADMIN')) {

                    // Update Caption
                    $upload->public = $public;
                    $upload->save();

                    return response()->json([
                        'status' => "success"
                    ]);

                } else {
                    return response()->json([
                        'status' => "failure",
                        'message' => "Unauthorized Access 1"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => "failure",
                    'message' => "Upload not found"
                ]);
            }
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }

    /**
     * Remove the specified upload from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete_file()
    {
        if(Module::hasAccess("Uploads", "delete")) {
            $file_id = Input::get('file_id');

            $upload = Upload::find($file_id);
            if(isset($upload->id)) {
                if($upload->user_id == Auth::user()->id || LaravelEntrust::hasRole('SUPER_ADMIN')) {

                    // Update Caption
                    $upload->delete();

                    return response()->json([
                        'status' => "success"
                    ]);

                } else {
                    return response()->json([
                        'status' => "failure",
                        'message' => "Unauthorized Access 1"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => "failure",
                    'message' => "Upload not found"
                ]);
            }
        } else {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access"
            ]);
        }
    }
}

