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

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::model($language, ['route' => [config('crmadmin.adminRoute') . '.languages.update', $language->id ], 'method'=>'PUT', 'id' => 'language-edit-form']) !!}
                    @la_form($module)

                    {{--
                    @la_input($module, 'name')
					@la_input($module, 'locale')
                    --}}
                    <br>
                    <div class="form-group">
                        {!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <a href="{{ url(config('crmadmin.adminRoute') . '/languages') }}" class="btn btn-default pull-right">Cancel</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $("#language-edit-form").validate({

    });
});
</script>
@endpush
