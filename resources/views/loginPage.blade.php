@extends('login')

@section('content')
<div class="col-md-3">

</div>
<div class="col-md-6">
    {!! Form::open(array('role' => 'form', 'accept-charset' => 'utf-8', 'class' => 'form-signin', 'url' => 'login/check-user')) !!}
        <h2 class="form-signin-heading">Login</h2>
        <div class="login-wrap">
            <div class="user-login-info">
                <div class="input text required">
                    <input type="text" name="username" class="form-control" placeholder="Username" autofocus="" required="required" id="username">
                </div>
                <div class="input password required">
                    <input type="password" name="password" class="form-control" placeholder="Password" required="required" id="password">
                </div>            </div>
            <label class="checkbox">
            </label>
            <button type="submit" class="btn btn-lg btn-login btn-block">Login</button>        </div>
    </form>
</div>

@endsection
