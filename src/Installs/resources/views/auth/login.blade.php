@extends('crm.layouts.auth')

@section('htmlheader_title')
    Log in
@endsection

@section('content')
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/home') }}"><b>{{ LAConfigs::getByKey('sitename_part1') }} </b>{{ LAConfigs::getByKey('sitename_part2') }}</a>
        </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Rất tiếc!</strong>Có một số vấn đề với thông tin của bạn.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="login-box-body">
    <p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc của bạn</p>
    <form action="{{ url('/login') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email" name="email"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Đăng nhập</button>
            </div><!-- /.col -->
        </div>
    </form>

    @include('auth.partials.social_login')
    <div class="row">

        <div class="col-xs-6">
            <a href="{{ url('/register') }}">Đăng ký mới</a>
        </div>
        <div class="col-xs-6">
            <a href="{{ url('/password/reset') }}"  class="pull-right">Lấy lại mật khẩu</a>
        </div>
    </div>

</div><!-- /.login-box-body -->

</div><!-- /.login-box -->

    @include('crm.layouts.partials.scripts_auth')

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

@endsection
