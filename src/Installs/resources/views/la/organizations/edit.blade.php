@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('crmadmin.adminRoute') . '/organizations') }}">Organization</a> :
@endsection
@section("contentheader_description", $organization->$view_col)
@section("section", "Organizations")
@section("section_url", url(config('crmadmin.adminRoute') . '/organizations'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Organizations Edit : ".$organization->$view_col)

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
		<ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
            <li class=""><a href="#" class="close1" data-dismiss="modal" aria-label="Close"  data-toggle="tooltip" data-placement="right" title="Close"><i class="fa fa-chevron-left"></i></a></li>
            @if(isset($pvd_language))
                @foreach ($pvd_language as $key=>$value)
                    <?php $class =  ($value->id == config('app.locale_id')) ? 'active' : ''; ?>
                    <li class="{{$class}}"><a role="tab" data-toggle="tab" class="{{$class}}" href="#tab-{{$value->locale}}" data-target="#tab-{{$value->locale}}"><img src="{{ IdeaHelper::pathImage($value->image,"30x20") }}" style="width: 30px; padding-right: 3px;" /> {{$value->name}}</a></li>
                @endforeach
            @endif
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -14px; margin-right: -9px;"><span aria-hidden="true">&times;</span></button>
        </ul>
	</div>
    {!! Form::model($organization, ['route' => [config('crmadmin.adminRoute') . '.organizations.update', $organization->id ], 'method'=>'PUT', 'id' => 'organization-edit-form']) !!}
        <div class="tab-content">

            @if(isset($pvd_language))
                @foreach ($pvd_language as $key => $value_lang)
                    <?php $class_active = (!empty($value_lang->id == config('app.locale_id'))) ? 'active': '';  ?>
                    <div role="tabpanel" class="tab-pane {{$class_active}} fade in" id="tab-{{$value_lang->locale}}">
                        <div class="tab-content">
                            <div class="panel-default panel-heading">
                                <h4>{{$value_lang->name}}</h4>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">

                                        @la_form_language($module,[], $value_lang)

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="form-group">
                    {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('laraadmin.adminRoute') . '/organizations') }}" class="btn btn-default pull-left">Cancel</a>
                </div>
            @else
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">

                            @la_form($module)

                            <div class="form-group">
                                {!! Form::submit( 'Update', ['class'=>'btn btn-success pull-right']) !!} <a href="{{ url(config('laraadmin.adminRoute') . '/organizations') }}" class="btn btn-default pull-left">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    {!! Form::close() !!}
</div>

@endsection

@push('scripts')
<script>
$(function () {
	$("#organization-edit-form").validate({

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
