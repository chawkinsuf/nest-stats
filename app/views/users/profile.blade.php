<div class="row">
	<div class="module col-sm-6 col-md-4">
		<div class="module-inner">
		{{ Form::model( Auth::user(), ['url'=>'users/profile', 'class'=>'form-update'] ) }}
			<h3>Update your profile</h3>
			<div class="form-group">
				{{ Form::email( 'email', null, ['class'=>'form-control', 'placeholder'=>'Email Address', 'required'] ) }}
			</div>
			<div class="form-group">
				{{ Form::password( 'password', ['class'=>'form-control', 'placeholder'=>'Password'] ) }}
			</div>
			<div class="form-group">
				{{ Form::password( 'password_confirmation',  ['class'=>'form-control', 'placeholder'=>'Confirm Password'] ) }}
			</div>
			<div class="form-group">
				{{ Form::text( 'nest_password', null, ['class'=>'form-control', 'placeholder'=>'Nest Password'] ) }}
			</div>
			{{ Form::submit( 'Update', ['class'=>'btn btn-lg btn-primary btn-block'] ) }}
		{{ Form::close() }}
		</div>
	</div>

	<div class="module col-sm-6 col-md-4">
		<div class="module-inner">
			<h3>Device list</h3>
			@if ( Auth::user()->nest_password === null )
			<p>Please enter a nest password so your account can be accessed.</p>
			@else
			<button href="{{ URL::to('users/devices') }}" class="btn btn-success has-spinner device-list">
				<span class="text">Refresh device list</span><span class="spinner">&nbsp;&nbsp;<i class="fa fa-refresh fa-spin"></i></span>
			</button>
			@endif
			<ul class="device-list"></ul>
		</div>
	</div>

	<div class="module col-sm-6 col-md-4">
		<div class="module-inner">
			<h3>Device information</h3>
			@if ( Auth::user()->nest_password === null )
			<p>Please enter a nest password so your account can be accessed.</p>
			@elseif ( $devices->count() == 0 )
			<p>No devices have been discovered.</p>
			@else
			devices...
			@endif
		</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	var handlebarsDeviceList = Handlebars.compile( $('#handlebars-device-list').html() );
	$('ul.device-list').html( handlebarsDeviceList({ devices: {{ $devicesData }} }) );
	$( 'button.device-list' ).click(function( event ){
		event.preventDefault();
		var button = $( this );
		$('ul.device-list').html('');
		$.ajax({
			type: 'get',
			url: button.attr( 'href' ),
			data: {}
		})
		.done(function( data, textStatus, jqXHR ){
			if ( !data || data.error ) {
				alertify.error( data.message || 'There was a problem updating the device list' );
				return;
			}

			alertify.success( 'Devices updated' );
			$('ul.device-list').html( handlebarsDeviceList( data ) );
		})
		.fail(function( jqXHR, textStatus, errorThrown ){
			alertify.error( 'There was a problem updating the device list: ' + errorThrown );
		})
		.always(function(){
			button.prop('disabled', false);
			button.toggleClass('spinning');
		});
	});

	$('.form-update').ajaxform({
		done: function( data, textStatus, jqXHR ) {
			if ( !data || data.error ) {
				alertify.error( data.message || 'There was a problem updating your profile' );
				return;
			}
			alertify.success( 'Model added' );
		},
		fail: function( jqXHR, textStatus, errorThrown ) {
			alertify.error( 'There was a problem updating your profile: ' + errorThrown );
		}
	});
});
</script>

<script id="handlebars-device-list" type="text/x-handlebars-template">
@{{# devices }}
	<li>
	@{{ name }} &mdash;
	{{ HTML::link( '#', 'Current data' ) }} &mdash;
	@{{# if tracking }}
		{{ HTML::link( '#', 'Stop tracking' ) }}
	@{{ else }}
		{{ HTML::link( '#', 'Start tracking' ) }}
	@{{/ if }}
	</li>
@{{/ devices }}
</script>
