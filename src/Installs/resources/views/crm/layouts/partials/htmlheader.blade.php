<head>
    <meta charset="UTF-8">
    <title>@hasSection('htmlheader_title')@yield('htmlheader_title') - @endif{{ LAConfigs::getByKey('sitename') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset('la-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Font awesome --}}
    <link href="{{ asset('la-assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('la-assets/css/ionicons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme style -->

    <link href="{{ asset('la-assets/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link href="{{ asset('la-assets/css/skins/'.LAConfigs::getByKey('skin').'.css') }}" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{ asset('la-assets/plugins/iCheck/square/blue.css') }}" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="{{ asset('la-assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">


    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('la-assets/plugins/tinymce/tinymce.min.js') }}"></script>

    @stack('styles')

</head>
