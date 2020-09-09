@extends("crm.layouts.app")

@section("contentheader_title", "Properties")
@section("contentheader_description", "Properties listing")
@section("section", "Properties")
@section("sub_section", "Listing")
@section("htmlheader_title", "Properties Listing")

@section("headerElems")
@la_access("Properties", "create")
    <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Property</button>
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

@la_access("Properties", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="box-header">
                @if(isset($pvd_language))
                    <ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
                        <li class=""><a href="{{ url(config('crmadmin.adminRoute') . '/properties') }}" class="close" data-dismiss="modal" aria-label="Close"  data-toggle="tooltip" data-placement="right" title="Close"><i class="fa fa-chevron-left"></i></a></li>
                        @foreach ($pvd_language as $key=>$value)
                            <?php $class =  ($value->id == config('app.locale_id')) ? 'active' : ''; ?>
                            <li class="{{$class}}"><a role="tab" data-toggle="tab" class="{{$class}}" href="#tab-{{$value->locale}}" data-target="#tab-{{$value->locale}}"  style='padding: 10px 5px;'><img src="{{ IdeaHelper::pathImage($value->image,"30x20") }}" style="width: 30px; padding-right: 3px;" /> {{$value->name}}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
            {!! Form::open(['action' => 'CRM\PropertyController@store', 'id' => 'property-add-form']) !!}
                <div class="tab-content">
                    @if(isset($pvd_language))
                        @foreach ($pvd_language as $key => $value_lang)
                            <?php $class_active = (!empty($value_lang->id == config('app.locale_id'))) ? 'active': '';  ?>
                            <div role="tabpanel" class="tab-pane {{$class_active ?? '' }} fade in" id="tab-{{$value_lang->locale}}">
                                <div class="tab-content">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @la_form_language($module,[], $value_lang)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('crmadmin.adminRoute') . '/properties') }}" class="btn btn-default pull-left">Cancel</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="modal-body">
                            <div class="box-body">
                                @la_form($module)

                                {{--
                                @la_input($module, 'name')
                                @la_input($module, 'description')
                                @la_input($module, 'value')
                                @la_input($module, 'unit')
                                @la_input($module, 'type_data')
                                @la_input($module, 'filter')
                                @la_input($module, 'show_colum')
                                @la_input($module, 'user_id')
                                --}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
                        </div>
                    @endif
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endla_access

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/iCheck/flat/green.css') }}"/>
<style>
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

</style>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('la-assets/plugins/iconpicker/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('la-assets/plugins/iCheck/icheck.min.js') }}"></script>
<script>
$(function () {
    $("#example1").DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url(config('crmadmin.adminRoute') . '/property_dt_ajax') }}",
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
        @if($show_actions)
        columnDefs: [ { orderable: false, targets: [-1] }],
        @endif
    });
    $("#property-add-form").validate({

    });
});

    $(function () {
        //Flat red color scheme for iCheck
        $('input[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass   : 'iradio_flat-green'
        })
        $('#icon input').iconpicker();

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
            	url : "{{ url(config('crmadmin.adminRoute') . '/property_field_switch') }}",
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
