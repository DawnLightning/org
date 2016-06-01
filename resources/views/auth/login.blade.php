<form method="POST" action="{{ action('Auth\AuthController@getLogin') }}">
    {!! csrf_field() !!}

    <div>
        Phone
        <input type="text" name="phone" value="{{ old('phone') }}">
    </div>

    <div>
        Password
        <input type="password" name="password" id="password">
    </div>

    <div>
        <input type="checkbox" name="remember"> Remember Me
    </div>

    <div>
        <button type="submit">Login</button>
    </div>
</form>