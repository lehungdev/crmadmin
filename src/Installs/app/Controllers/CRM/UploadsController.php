<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Dwij IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Input;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;

use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Helpers\LAHelper;
use Shanmuga\LaravelEntrust\Facades\LaravelEntrustFacade as LaravelEntrust;

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
        $module = Module::get('Uploads');
        if(Module::hasAccess($module->id)) {
            return View('crm.uploads.index', [
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
    public function get_file(Request $request, $hash, $name)
    {

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
            $size = $request->get('s');
            if(isset($size)) {
                if(!is_numeric($size)) {
                    $size = 150;
                }
                $thumbpath = public_path("thumbnails/".basename($upload->path)."-".$size."x".$size);

                if(File::exists($thumbpath)) {
                    $path = $thumbpath;
                } else {
                    // Create Thumbnail
                    LAHelper::createThumbnail($upload->path, $thumbpath, $size, $size, "transparent");
                    $path = $thumbpath;
                }
            }

            $file = File::get($path);
            $type = File::mimeType($path);

            $download = $request->get('download');
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
    public function upload_files(Request $request) {

        if(Module::hasAccess("Uploads", "create")) {
            $input = $request->all();

            if($request->hasFile('file')) {

                $rules = array(
                    'file' => 'mimes:jpg,jpeg,bmp,png,pdf|max:3000',
                );
                $validation = Validator::make($input, $rules);
                if ($validation->fails()) {
                    return response()->json($validation->errors()->first(), 400);
                }

                $file = $request->file('file');

                //$folder = public_path('uploads');

                if(!empty($request->input('folder_end'))){
                        $folder_end1 =  $request->input('folder_end');
                        $folder_end =  '/'.$folder_end1;
                } else {
                    $folder_end1 = '';
                    $folder_end = '';
                }

                $folder = 'uploads'.$folder_end;

                $filename = $file->getClientOriginalName();
                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                $filename_sort = Str::slug(Str::substr( $filename, 0, strlen($filename) - strlen($extension) - 1));
                $filename      = $filename_sort.'.'.$extension;

                $date_append = date("Y-m-d-His-");

                $upload_success = $request->file('file')->move($folder, $date_append.$filename);

                if( $upload_success ) {
                    // Get public preferences
                    // config("crmadmin.uploads.default_public")
                    $public = $request->get('public');
                    if(isset($public)) {
                        $public = true;
                    } else {
                        $public = false;
                    }

                    $upload = Upload::create([
                        "name" => $filename,
                        "path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
                        "extension" => pathinfo($filename, PATHINFO_EXTENSION),
                        "caption" => $folder_end1,
                        "hash" => "",
                        "public" => $public,
                        "user_id" => Auth::user()->id
                    ]);
                    // apply unique random hash to file
                    while(true) {
                        $hash = strtolower(Str::random(20));
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
    public function uploaded_files(Request $request)
    {
        if(Module::hasAccess("Uploads", "view")) {
            $uploads = array();
            /*
             * Get caption
             * */
            $path_end = explode("/",$request->path());
            if(count($path_end) > 0 and $path_end[count($path_end) - 1] != 'uploaded_files')
                $folder_end =  $path_end[count($path_end) - 1];
            else $folder_end = '';

            // print_r(Auth::user()->roles);
            if(LaravelEntrust::hasRole('SUPER_ADMIN')) { //dd($folder_end);
                // $uploads = Upload::where('caption', $folder_end)->get();
                if(!empty($folder_end))
                    $uploads = Upload::where('caption', $folder_end)->get();
                else
                    $uploads = Auth::user()->uploads;

            } else {
                // if(config('crmadmin.uploads.private_uploads')) {
                //     // Upload::where('user_id', 0)->first();
                //     $uploads = Auth::user()->uploads;
                // } else {
                    if(!empty($folder_end))
                        $uploads = Upload::where('caption', $folder_end)->get();
                    else
                        $uploads = Auth::user()->uploads;
                // }
            }

            $uploads2 = array();
            foreach ($uploads as $upload) {
                $u = (object) array();
                $u->id = $upload->id;
                $u->name = $upload->name;
                $path = explode("/",$upload->path);
                $date_append = Str::substr($path[count($path)-1], 2, 15 );
                $u->date = $date_append;
                $u->extension = $upload->extension;
                $u->hash = $upload->hash;
                $u->public = $upload->public;
                $u->caption = $upload->caption;
                $u->user = $upload->user->name;

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
            // dd($uploads2);
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
    public function update_caption(Request $request)
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = $request->get('file_id');
            $caption = $request->get('caption');

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
    public function update_filename(Request $request)
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = $request->get('file_id');
            $filename = $request->get('filename');

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
    public function update_public(Request $request)
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $file_id = $request->get('file_id');
            $public = $request->get('public');
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
    public function delete_file(Request $request)
    {
        if(Module::hasAccess("Uploads", "delete")) {
            $file_id = $request->get('file_id');

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
