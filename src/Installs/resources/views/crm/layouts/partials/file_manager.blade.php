<script src="{{ asset('la-assets/plugins/dropzone/dropzone.js') }}"></script>
<?php
$path_site = explode(config('crmadmin.adminRoute'),\Request::path());
$path_site = $path_site[1];
$path_site_item = explode("/",$path_site);
if(count($path_site_item) > 0 and !empty($path_site_item[1]))
    $folder_end =  $path_site_item[1];
else $folder_end = '';
$folder_end123 = $folder_end;
//dd($path_site);
?>

<div class="modal fade" id="fm" role="dialog" aria-labelledby="fileManagerLabel">
	<input type="hidden" id="image_selecter_origin" value="">
	<input type="hidden" id="image_selecter_origin_type" value="">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="fileManagerLabel">Select File</h4>
			</div>
			<div class="modal-body p0">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="fm_folder_selector">
							<form action="{{ url(config('crmadmin.adminRoute') . '/upload_files')}}" id="fm_dropzone" enctype="multipart/form-data" method="POST">
								{{ csrf_field() }}
								<div class="dz-message"><i class="fa fa-cloud-upload"></i><br>Drop files here to upload</div>

								@if(!config('crmadmin.uploads.private_uploads'))
									<label class="fm_folder_title">Is Public ?</label>
									{{ Form::checkbox("public", "public", config("crmadmin.uploads.default_public"), []) }}
									<input type="hidden" value="{{ $folder_end  }}" name="folder_end" />
									<div class="Switch Ajax Round Off"><div class="Toggle"></div></div>
								@endif
							</form>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 pl0_">
						<div class="nav">
							<div class="row1">
								<div class="col-xs-2 col-sm-7 col-md-7"></div>
								<div class="col-xs-10 col-sm-5 col-md-5">
									<input type="search" class="form-control pull-right" placeholder="Search file name">
								</div>
							</div>
						</div>
						<div class="fm_file_selector">
							<ul>

							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>