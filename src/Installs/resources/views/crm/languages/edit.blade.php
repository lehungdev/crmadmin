@extends("crm.layouts.app")

@section("contentheader_title")
    <a href="{{ url(config('crmadmin.adminRoute') . '/languages') }}">Language</a> :
@endsection
@section("contentheader_description", $language->$view_col)
@section("section", "Languages")
@section("section_url", url(config('crmadmin.adminRoute') . '/languages'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Languages Edit : ".$language->$view_col)

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
                <li class=""><a href="{{ url(config('crmadmin.adminRoute') . '/languages') }}" class="close" data-dismiss="modal" aria-label="Close"  data-toggle="tooltip" data-placement="right" title="Close"><i class="fa fa-chevron-left"></i></a></li>
                    @foreach ($pvd_language as $key=>$value)
                        <?php $class =  ($value->id == config('app.locale_id')) ? 'active' : ''; ?>
                        <li class="{{$class}}"><a role="tab" data-toggle="tab" class="{{$class}}" href="#tab-{{$value->locale}}" data-target="#tab-{{$value->locale}}"><img src="{{ IdeaHelper::pathImage($value->image,"30x20") }}" style="width: 30px; padding-right: 3px;" /> {{$value->name}}</a></li>
                    @endforeach
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -14px; margin-right: -9px;"><span aria-hidden="true">&times;</span></button>
            </ul>
        @endif
    </div>
    {!! Form::model($language, ['route' => [config('crmadmin.adminRoute') . '.languages.update', $language->id ], 'method'=>'PUT', 'id' => 'language-edit-form']) !!}
        <div class="tab-content">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                            @la_form($module)

                            </div>
                            {{--
                            @la_input($module, 'name')
					@la_input($module, 'image')
					@la_input($module, 'locale')
                            --}}

                            <div class="form-group">
                                {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('crmadmin.adminRoute') . '/languages') }}" class="btn btn-default pull-left">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
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
