@extends("crm.layouts.app")

@section("contentheader_title")
    <a href="{{ url(config('crmadmin.adminRoute') . '/categories') }}">Category</a> :
@endsection
@section("contentheader_description", $category->$view_col)
@section("section", "Categories")
@section("section_url", url(config('crmadmin.adminRoute') . '/categories'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Categories Edit : ".$category->$view_col)

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
                <li class=""><a href="{{ url(config('crmadmin.adminRoute') . '/categories') }}" class="close" data-dismiss="modal" aria-label="Close"  data-toggle="tooltip" data-placement="right" title="Close"><i class="fa fa-chevron-left"></i></a></li>
                    @foreach ($pvd_language as $key=>$value)
                        <?php $class =  ($value->id == config('app.locale_id')) ? 'active' : ''; ?>
                        <li class="{{$class}}"><a role="tab" data-toggle="tab" class="{{$class}}" href="#tab-{{$value->locale}}" data-target="#tab-{{$value->locale}}" style='padding: 0px 10px; line-height: 40px;'><img src="{{ IdeaHelper::pathImage($value->image,"30x20") }}" style="width: 30px; padding-right: 3px;" /> {{$value->name}}</a></li>
                    @endforeach
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -14px; margin-right: -9px;"><span aria-hidden="true">&times;</span></button>
            </ul>
        @endif
    </div>
    {!! Form::model($category, ['route' => [config('crmadmin.adminRoute') . '.categories.update', $category->id ], 'method'=>'PUT', 'id' => 'category-edit-form']) !!}
        <div class="tab-content">
            @if(isset($pvd_language))
                @foreach ($pvd_language as $key => $value_lang)
                    <?php $class_active = (!empty($value_lang->id == config('app.locale_id'))) ? 'active': '';  ?>
                    <div role="tabpanel" class="tab-pane {{$class_active ?? '' }} fade in" id="tab-{{$value_lang->locale}}">
                        <div class="tab-content">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- col-md-offset-2 --}}
                                        @la_form_language($module,[], $value_lang)

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="modal-footer">
                    <div class="col-md-12">
                        {{-- col-md-8 col-md-offset-2 --}}
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        {!! Form::submit( 'Submit', ['class'=>'btn btn-success pull-right']) !!}
                    </div>
                </div>
            @else
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">

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

                            <div class="form-group">
                                {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('crmadmin.adminRoute') . '/categories') }}" class="btn btn-default pull-left">Cancel</a>
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
        $("#category-edit-form").validate({

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
@endpush
