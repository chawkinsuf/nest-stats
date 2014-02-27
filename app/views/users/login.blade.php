{{ Form::open([ 'url'=>'users/login', 'class'=>'form-login login-page' ]) }}
	<h2>Please Login</h2>
	<input type="email" name="email" class="form-control" placeholder="Email address" value="{{ $email }}" required autofocus>
	<input type="password" name="password" class="form-control" placeholder="Password" required>
	<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
{{ Form::close() }}