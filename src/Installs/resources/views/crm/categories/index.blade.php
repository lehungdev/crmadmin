@extends("crm.layouts.app")
<?php
use Dwij\Laraadmin\Models\Module;
use App\Models\Real_estate;
use App\Models\Properties_cat;
use App\Models\Module_table;
use App\Http\Controllers\Helpers\IdeaHelper;
?>
@section("contentheader_title", "Categories")
@section("contentheader_description", "Categories listing")
@section("section", "Categories")
@section("sub_section", "Listing")
@section("htmlheader_title", "Categories Listing")

@section("headerElems")
@la_access("Categories", "create")
    <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Category</button>
@endla_access
@endsection

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row" style="margin-left: -10px; margin-right: -10px;">
    <?php $i = 0; $color_array = ['bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive','bg-fuchsia','bg-aqua', 'bg-green','bg-yellow','bg-maroon','bg-red','bg-olive'] ?>
    @foreach ($allCategory as $key =>  $catItem)
        @if(empty($catItem->parent))
			<?php $i++; ?>
            <div class="allCategory col-lg-2 col-xs-6" >
                <!-- small box -->
                <div class="small-box {{ $color_array[$i] }}">
                    <div class="inner">
                        <h4>{{$catItem->name }}</h4>
                        <p><b style="font-size: 16px;">({{ 'count' }})</b> <a class="add_real_estates" data-properties="{{ 'data_properties' }}" cat-id="{{ $catItem->id }}" title="{{ $catItem->name }}" data-toggle="modal" data-target="#AddModal" style="color: #f0f0f0; cursor: pointer;">Thêm mới</a></p>
                    </div>
                    <div class="icon">
                        <i class="fa {{ $catItem->icon }}"></i>
                    </div>
                    <a href="{{url(config('crmadmin.adminRoute') . '/categories/' . $catItem->id)}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        @endif
    @endforeach

</div><!-- /.row -->
<div class="box box-success menus">
    <!--<div class="box-header"></div>-->
    <div class="box-body">
        <div class="row">


            <div class="col-md-6 col-lg-6">
                <div class="dd" id="menu-nestable">
                    <ol class="dd-list">
                        @foreach ($allCategory as $catItem)
                            @if(empty($catItem->parent))
                                <?php
                                    $show_menu = IdeaHelper::print_menu_editor($catItem, 'categories', $allCategory);
                                    // dd($show_menu['catAll']);
                                    $allCategory = $show_menu['catAll'] ?? '';
                                ?>
                            @endif
                        @endforeach
                    </ol>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li ><a href="#tab-modules" data-toggle="tab">Hiển thị</a></li>
                        <li class="active"><a href="#tab-custom-link" data-toggle="tab">Thêm nhóm mới</a></li>
                    </ul>

                    <div class="tab-content"  style="padding:  0px 15px 20px 15px;">

                        <div class="tab-pane " id="tab-modules">
                            <ul>
                                @if(!empty($modules))
                                    @foreach ($modules as $module_item)

                                        <li><i class="fa {{ $module_item->icon }}"></i> {{ $module_item->name }} @if (empty($module_item->publish)) <a menu_id="{{ $module_item->id }}" status="1" class="addModuleMenu pull-right"><i class="fa fa-plus"></i></a> @endif</li>

                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="tab-pane active" id="tab-custom-link">
                            <div class="box-header row">
                                <ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
                                    @if(isset($pvd_language))
                                        @foreach ($pvd_language as $key=>$value)
                                            <?php $class =  ($value->id == config('app.locale_id')) ? 'active' : ''; ?>
                                            <li class="{{$class}}"><a role="tab" data-toggle="tab" class="{{$class}}" href="#tabb-{{$value->locale}}" data-target="#tabb-{{$value->locale}}" style='padding: 0px 10px; line-height: 40px;'><img src="{{ \IdeaHelper::pathImage($value->image,"30x20") }}" style="width: 30px; padding-right: 3px;" /> {{$value->name}}</a></li>
                                        @endforeach
                                    @endif

                                </ul>
                            </div>

                            {!! Form::open(['action' => 'CRM\CategoryController@store', 'id' => 'category-add-form']) !!}
                                @if(isset($pvd_language))
                                    @foreach ($pvd_language as $key => $value_lang)
                                        <?php $class_active = (!empty($value_lang->id == config('app.locale_id'))) ? 'active': '';  ?>
                                        <div role="tabpanel" style="display: none;"  class="tab-pane tab-pane-cat {{$class_active}} fade in" id="tabb-{{$value_lang->locale}}">
                                            @la_form_language($module,[], $value_lang)

                                        </div>
                                    @endforeach
                                @endif
                                {{-- @la_form($module) --}}
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success pull-right']) !!}
                                </div>

                            {!! Form::close() !!}

                        </div>
                    </div><!-- /.tab-content -->
                </div><!-- nav-tabs-custom -->
            </div>


        </div>
    </div>
</div>
<div class="box box-success">
    <div class="box-body table-responsive" id="resize_wrapper" >
        <table id="example1" class="table table-striped table-bordered dt-responsive nowrap dataTable no-footer dtr-inline collapsed" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
            <thead>
                <tr class="success">
                    @foreach( $listing_cols as $col )
                        <th>{{ $module->fields[$col]['label'] ?? ucfirst($col) }}</th>
                    @endforeach
                    @if($show_actions)
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@la_access("Categories", "create")
<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Category</h4>
            </div>
            {!! Form::open(['action' => ['CRM\CategoryController@update', 1], 'id' => 'menu-edit-cat']) !!}
            <div class="modal-body">
                <div class="box-body">
                    @la_form($module)

                    {{--
                    @la_input($module, 'name')
					@la_input($module, 'parent')
					@la_input($module, 'hierarchy')
					@la_input($module, 'slug')
					@la_input($module, 'image')
					@la_input($module, 'icon')
					@la_input($module, 'property')
					@la_input($module, 'publish')
                    --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endla_access

@endsection

@push('styles')
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
{{-- <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css"/> --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/bootstrap-slider/slider.css') }}"/>

<link type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet" />
<link type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet" />
<style>
    .tab-pane-cat.active {
        display: block !important;
    }
    .dd3-content {
        max-width: 480px;
    }
    .dd3-handle{
        overflow: initial;
        text-indent: 0;
        padding: 0;
        text-align: center;
        line-height: 30px;
    }

    .dd3-handle:before {
        display: none;

    }

    @media (min-width: 768px){
        .modal-dialog {
            width: 88%;
            margin: 30px auto;
        }
    }
    @media (min-width: 920px){
        .modal-dialog {
            width: 920px;
            margin: 30px auto;
        }
    }
    .slider.slider-horizontal .slider-handle {
        margin-top: -2px;
    }
    .slider-disabled .slider-selection {
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .slider.slider-horizontal .slider-selection {
        height: 100%;
        top: 0;
        bottom: 0;
        /* border: 1px solid #f2f1f1; */
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
        background-image: linear-gradient(to bottom, #e6f3e1, #e6f3e1);
    }
    .slider.slider-horizontal .slider-track {
        height: 18px;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .slider-selection {

    }
    .slider .tooltip{display:none !important;}
    .slider.gray .slider-handle{background-color:#888;}
    .slider.orange .slider-handle{background-color:#FF9800;}
    .slider.green .slider-handle{background-color:#00a65a;}
    button.dt-button, div.dt-button, a.dt-button {
        background-color: #10cfbd;
        border-color: #0eb7a7;
        border-radius: 3px;
        box-shadow: none;
        border: 1px solid transparent;
        background-image: none;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<!--script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script-->
<script src="{{ asset('la-assets/plugins/nestable/jquery.nestable.js') }}"></script>
<script src="{{ asset('la-assets/plugins/iconpicker/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>

{{-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> --}}
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<!--script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script-->
<script src="{{ asset('la-assets/plugins/bootstrap-slider/bootstrap-slider.js') }}"></script>
<script src="{{ asset('la-assets/plugins/jQueryUI/jquery-ui.js') }}"></script>
<script>
$(function () {
    $('input[name=icon]').iconpicker();

    $('#menu-nestable').nestable({
        group: 1
    });

    $('#menu-nestable').on('change', function() {
        var jsonData = $('#menu-nestable').nestable('serialize');
        var item = $(this);
        $.ajax({
            url: "{{ url(config('crmadmin.adminRoute') . '/ca_menus/update_hierarchy') }}",
            method: 'POST',
            data: {
                jsonData: jsonData,
                "_token": '{{ csrf_token() }}'
            },
            success: function( data ) {
                    console.log(data);
            }
        });
    });

    $('.display_sub').click(function() {
        var id = $(this).attr("id");
        if($(this).find('i').attr("class") == 'fa fa-minus' ) {
            $(this).find('i').removeClass('fa fa-minus');
            $(this).find('i').addClass('fa fa-plus');
            $('#'+id).parent().parent().parent('li').find('>ol').css('display','none');
        }
        else {
            $(this).find('i').removeClass('fa fa-plus');
            $(this).find('i').addClass('fa fa-minus');
            $('#'+id).parent().parent().parent('li').find('>ol').css('display','block');
        }

    });

    $("#tab-modules .addModuleMenu, #menu-nestable .addModuleMenu").on("click", function() {
        var menu_id = $(this).attr("menu_id");
        var status = $(this).attr("status");
        $.ajax({
            url: "{{ url(config('laraadmin.adminRoute') . '/categories/add') }}",
            method: 'POST',
            data: {
                menu_id: menu_id,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function( data ) {

                window.location.reload();

            }
        });
    });

    $("#menu-nestable .editMenuBtn").on("click", function() {
        var info = JSON.parse($(this).attr("info"));
        var menu_id =  $(this).attr("menu_id");
        if($( "#EditModal form .tab-content .tab-pane input#menu_id" ).length){
            $('#EditModal form .tab-content .tab-pane input#menu_id').val(menu_id);
        } else {
            $('#EditModal form .tab-content .tab-pane').append('<input  id = "menu_id" name = "menu_id" type = "hidden" value="'+ menu_id +'" />');
        }
        $( "#EditModal form .tab-content .tab-pane" ).each(function( index ) {
            var id_langgue = $(this).attr('data-id');
            //Thêm local_parent
            if(info[id_langgue]) {

                if ($('#EditModal form .tab-content .tab-pane input[name="id[' + id_langgue + ']"]').length) {
                    $('#EditModal form .tab-content .tab-pane input[name="id[' + id_langgue + ']"]').val(info[id_langgue]['id']);
                } else {
                    $('#EditModal form .tab-content .tab-pane').append('<input  id = "id" name = "id[' + id_langgue + ']" type = "hidden" value="' + info[id_langgue]['id'] + '" />');
                }

                if ($('#EditModal form .tab-content .tab-pane input[name="local_parent[' + id_langgue + ']"]').length) {
                    $('#EditModal form .tab-content .tab-pane input[name="local_parent[' + id_langgue + ']"]').val(menu_id);
                } else {
                    $('#EditModal form .tab-content .tab-pane').append('<input  id = "" name = "local_parent[' + id_langgue + ']" type = "hidden" value="' + menu_id + '" />');
                }
            }

                //Quét toàn bộ thẻ input
                $( this ).find(' .modal-body div.form-group input').each(function( index1 ) {
                    var parent = $(this).parents('div.form-group');
                    var type_value = $(this).attr('type');

                    switch (type_value) {
                        case 'text':
                            $(this).val('');
                            if (info[id_langgue]) {
                                $(this).val(info[id_langgue][parent.attr('id')]);
                            }
                            break;
                        case 'hidden':
                            if ($(this).attr('value') != 'false') {
                                parent.find('a.btn_upload_image').attr('class', 'btn btn-default btn_upload_image');
                                parent.find('div.uploaded_image').attr('class', 'uploaded_image hide');
                                parent.find('input').val('');
                                parent.find('div.uploaded_image img').attr('src', '');

                                if (info[id_langgue]) {
                                    parent.find('a.btn_upload_image').attr('class', 'btn btn-default btn_upload_image hide');
                                    parent.find('div.uploaded_image').attr('class', 'uploaded_image');
                                    parent.find('input').val(info[id_langgue][parent.attr('id')]);
                                    parent.find('div.uploaded_image img').attr('src', info[id_langgue][parent.attr('id') + '_img'] + '');
                                }
                            }
                            break;
                        case 'checkbox':
                            var parent = $(this).parents('div.form-group');
                            parent.find('div.Switch.Round').removeClass('Off');
                            parent.find('div.Switch.Round').addClass('On');

                            if (info[id_langgue]) {
                                if (info[id_langgue][parent.attr('id')] == 1) {
                                    parent.find('input.form-control').attr('checked', 'checked');
                                    parent.find('div.Switch.Round').removeClass('On');
                                    parent.find('div.Switch.Round').addClass('Off');
                                } else {
                                    parent.find('div.Switch.Round').removeClass('Off');
                                    parent.find('div.Switch.Round').addClass('On');
                                }
                            }

                            break;
                        default:
                            $(this).val('');
                            if (info[id_langgue]) {
                                $(this).val(info[id_langgue][parent.attr('id')]);
                            }
                            // break;
                    }

                });

                //Quét toàn bộ thẻ textarea
                $( this ).find(' .modal-body div.form-group textarea').each(function( index1 ) {
                    var parent = $( this ).parents('div.form-group');
                    $(this).val('');
                    if(info[id_langgue]) {
                        $(this).val(info[id_langgue][parent.attr('id')]);
                    }

                });

                //Quét toàn bộ thẻ select
                $( this ).find(' .modal-body div.form-group select').each(function( index1 ) {
                    var parent = $( this ).parents('div.form-group');
                    $(this).find("option").prop("selected", false).trigger("change");
                    //$(this).find("option[value=" + info[id_langgue][parent.attr('id')] + "]").prop("selected", true).trigger("change");

                    if(info[id_langgue]) {
                        $(this).find("option").prop("selected", false).trigger("change");
                        $(this).find("option[value=" + info[id_langgue][parent.attr('id')] + "]").prop("selected", true).trigger("change");
                    }

                });
        });

        $("#EditModal").modal("show");



    });

    $("#example1").DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url(config('crmadmin.adminRoute') . '/category_dt_ajax') }}",
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
        @if($show_actions)
            columnDefs: [ { orderable: false, targets: [-1] }],
        @endif
    });
    $("#category-add-form").validate({ });

    $('#example1').on( 'draw.dt', function () {
        $('.slider').slider();
        // $('.slider').slider('disable');
        $(".slider.slider-horizontal").each(function(index) {
            var field = $(this).next().attr("name");
            var value = $(this).next().val();
            // console.log(""+field+" ^^^ "+value);
            switch (value) {
                case '0':
                    $(this).removeClass("orange");
                    $(this).removeClass("green");
                    $(this).addClass("gray");
                    break;
                case '1':
                    $(this).removeClass("gray");
                    $(this).removeClass("green");
                    $(this).addClass("orange");
                    break;
                case '2':
                    $(this).removeClass("gray");
                    $(this).removeClass("orange");
                    $(this).addClass("green");
                    break;
            }
        });
        $('.slider').bind('slideStop', function(event) {
            if($(this).next().attr("name")) {
                var field = $(this).next().attr("name");
                var value = $(this).next().val();
                var id = $(this).next().attr("data-slider-id");
                console.log(""+field+" = "+id);
                $.ajax({
                    type: "POST",
                    url : "{{ url(config('crmadmin.adminRoute') . '/category_field_slider_switch') }}",
                    data : {
                        _token: '{{ csrf_token() }}',
                        sliderValue: value,
                        sliderId: id,
                        sliderField: field,
                    },
                    success : function(data){
                        console.log(data);
                    }
                });
                if(value == 0) {
                    $(this).removeClass("orange");
                    $(this).removeClass("green");
                    $(this).addClass("gray");
                } else if(value == 1) {
                    $(this).removeClass("gray");
                    $(this).removeClass("green");
                    $(this).addClass("orange");
                } else if(value == 2) {
                    $(this).removeClass("gray");
                    $(this).removeClass("orange");
                    $(this).addClass("green");
                }
            }
        });
        //Flat red color scheme for iCheck
        // $('input[type="radio"]').iCheck({
        // checkboxClass: 'icheckbox_flat-green',
        // radioClass   : 'iradio_flat-green'
        // })
    });

    $("select.active_lang" ).prop("disabled", 'true');
    $("input.active_lang" ).prop("disabled", 'true');
    $(".active_lang" ).prop("disabled", 'true');
    $('form').on('submit', function() {
        $("select[name='local_parent']" ).prop("disabled", false);
        $("select[name='locale']" ).prop("disabled", false);
    });
});
</script>
@la_access("Properties", "create")
    <script>
        $('#example1').on("click", '.Switch',function() {
            var state = "false";
            if ($(this).hasClass('On')) {
                state = "true";
                $(this).removeClass("On");
                $(this).addClass("Off");
            } else {
                state = "false";
                $(this).removeClass("Off");
                $(this).addClass("On");
            }

            $.ajax({
            	type: "POST",
            	url : "{{ url(config('crmadmin.adminRoute') . '/category_field_switch') }}",
            	data : {
                    _token: '{{ csrf_token() }}',
                    switchName: $(this).attr("switchName"),
                    switchId: $(this).attr("switchId"),
            		state: state,
            	},
            	success : function(data){
            		console.log(data);
            	}
            });
        });

    </script>
@endla_access
@endpush