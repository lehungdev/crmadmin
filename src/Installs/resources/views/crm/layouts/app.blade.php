<!DOCTYPE html>
<html lang="en">

@section('htmlheader')
	@include('crm.layouts.partials.htmlheader')
@show
<body class="{{ LAConfigs::getByKey('skin') }} {{ LAConfigs::getByKey('layout') }} @if(LAConfigs::getByKey('layout') == 'sidebar-mini') sidebar-collapse @endif" bsurl="{{ url('') }}" adminRoute="{{ config('crmadmin.adminRoute') }}">
<div class="wrapper">

	@include('crm.layouts.partials.mainheader')

	@if(LAConfigs::getByKey('layout') != 'layout-top-nav')
		@include('crm.layouts.partials.sidebar')
	@endif

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		@if(LAConfigs::getByKey('layout') == 'layout-top-nav') <div class="container"> @endif
		@if(!isset($no_header))
			@include('crm.layouts.partials.contentheader')
		@endif

		<!-- Main content -->
		<section class="content {{ $no_padding ?? '' }}">
			<!-- Your Page Content Here -->
			@yield('main-content')
		</section><!-- /.content -->

		@if(LAConfigs::getByKey('layout') == 'layout-top-nav') </div> @endif
	</div><!-- /.content-wrapper -->

	@include('crm.layouts.partials.controlsidebar')

	@include('crm.layouts.partials.footer')

</div><!-- ./wrapper -->

@include('crm.layouts.partials.file_manager')

@section('scripts')
	@include('crm.layouts.partials.scripts')
@show

</body>
</html>
