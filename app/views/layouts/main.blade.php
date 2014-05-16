<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>NestStats - Get more data from your Nest</title>

	{{ HTML::style('lib/bootstrap/css/bootstrap.min.css') }}
	{{ HTML::style('lib/bootstrap/css/bootstrap-theme.min.css') }}
	{{ HTML::style('lib/font-awesome/css/font-awesome.min.css') }}
	{{ HTML::style('lib/alertify/css/alertify.core.css') }}
	{{ HTML::style('lib/alertify/css/alertify.bootstrap.css') }}
	{{ HTML::style('css/main.css')}}

	{{ HTML::script('lib/jquery/jquery.min.js') }}
	{{ HTML::script('lib/jquery/jquery-ui.widget.min.js') }}
	{{ HTML::script('lib/enquire/enquire.min.js') }}
	{{ HTML::script('lib/bootstrap/js/bootstrap.min.js') }}
	{{ HTML::script('lib/handlebars/handlebars.js') }}
	{{ HTML::script('lib/alertify/js/alertify.min.js') }}
	{{ HTML::script('lib/bootstrap-treeview/js/bootstrap-treeview.js') }}
	{{ HTML::script('lib/highstock/highstock.js') }}
	{{ HTML::script('lib/highstock/modules/exporting.js') }}
	{{ HTML::script('js/jquery.ajaxform.js') }}
	{{ HTML::script('js/main.js') }}
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-header">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">NestStats</a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse-header">
				<ul class="nav navbar-nav">
				@if ( Auth::check() )
					<li class="{{ Request::is('users/*')  ? 'active' : '' }}"><a href="{{ URL::to('users/profile') }}">Profile</a></li>
					<li class="dropdown {{ Request::is('graphs/*') ? 'active' : '' }}">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Graphs <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ URL::to('graphs/temperature') }}">Temperature</a></li>
							<li><a href="{{ URL::to('graphs/humidity') }}">Humidity</a></li>
						</ul>
					</li>
					<li><a href="{{ URL::to('users/logout') }}">Logout</a></li>
				@else
					<li class="dropdown">
						<a href="{{ URL::to('users/login') }}" class="dropdown-toggle" data-toggle="dropdown">Login <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li>
								{{ Form::open([ 'url'=>'users/login', 'class'=>'form-login login-nav navbar-form navbar-left' ]) }}
									<input type="email" name="email" class="form-control" placeholder="Email address" required autofocus>
									<input type="password" name="password" class="form-control" placeholder="Password" required>
									<button class="btn btn-primary btn-block" type="submit">Login</button>
								{{ Form::close() }}
							</li>
						</ul>
					</li>
				@endif
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">
		@if ( $errors->any() )
		<ul class="alert alert-danger">
			@foreach ( $errors->all() as $error )
			<li>{{ $error }}</li>
			@endforeach
		</ul>
		@endif

		@if ( Session::has('message') )
		<script type="text/javascript">
		$(function(){
			alertify.log('{{{ Session::get('message') }}}');
		});
		</script>
		@endif

		{{ $content }}
	</div>
</body>
</html>