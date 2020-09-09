@extends("crm.layouts.app")

@section("contentheader_title")
    <a href="{{ url(config('crmadmin.adminRoute') . '/properties') }}">Property</a> :
@endsection
@section("contentheader_description", $property->$view_col)
@section("section", "Properties")
@section("section_url", url(config('crmadmin.adminRoute') . '/properties'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Properties Edit : ".$property->$view_col)

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

<div class="box">
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
    {!! Form::model($property, ['route' => [config('crmadmin.adminRoute') . '.properties.update', $property->id ], 'method'=>'PUT', 'id' => 'property-edit-form']) !!}
        <div class="tab-content">
            @if(isset($pvd_language))
                @foreach ($pvd_language as $key => $value_lang)
                    <?php $class_active = (!empty($value_lang->id == config('app.locale_id'))) ? 'active': '';  ?>
                    <div role="tabpanel" class="tab-pane {{$class_active ?? '' }} fade in" id="tab-{{$value_lang->locale}}">
                        <div class="tab-content">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            @la_form_language($module,[], $value_lang)
                                        </div>
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
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">

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

                            <div class="form-group">
                                {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('crmadmin.adminRoute') . '/properties') }}" class="btn btn-default pull-left">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    {!! Form::close() !!}
</div>

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/iCheck/flat/green.css') }}"/>
@endpush
@push('scripts')
<script src="{{ asset('la-assets/plugins/iconpicker/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('la-assets/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    //Flat red color scheme for iCheck
    $('input[type="radio"]').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })
    $(function () {
        $('#icon input').iconpicker();
        $("#property-edit-form").validate({ });

        $("select.active_lang" ).prop("disabled", 'true');
        $("input.active_lang" ).prop("disabled", 'true');
        $(".active_lang" ).prop("disabled", 'true');
        $('form').on('submit', function() {
            $("select[name='local_parent']" ).prop("disabled", false);
            $("select[name='locale']" ).prop("disabled", false);
        });
    });
</script>
@endpush
