(function( $ ) {
	$.widget( "ui.ajaxform", {

		// Default options
		options: {
			validate: null,
			done: null,
			fail: null,
			always: null
		},

		// Set up the widget
		_create: function() {
			var self = this,
				options = self.options,
				element = self.element

			self.submit = self.element.find('input[type=submit],button[type=submit]');

			if ( self.options.validate === null ) {
				element.on('submit.ajaxform', $.proxy( self._submit, self ));
			} else {
				$.extend( self.options.validate, { submitHandler: $.proxy( self._submit, self ) } );
				$( element ).validate( self.options.validate );
			}
		},

		// Function to handle the for submission
		_submit: function( event ) {
			this.submit.button('loading');

			var request = $.ajax({
				type: 'POST',
				url: this.element.attr('action'),
				data: this.element.serialize()
			});

			request.done( $.proxy( this._done, this ) );
			request.fail( $.proxy( this._fail, this ) );
			request.always( $.proxy( this._always, this ) );

			return false;
		},

		_done: function( data, textStatus, jqXHR ) {
			if ( $.isFunction( this.options.done ) ) {
				this.options.done.apply( this.element, arguments );
			}
		},

		_fail: function( jqXHR, textStatus, errorThrown ) {
			if ( $.isFunction( this.options.fail ) ) {
				this.options.fail.apply( this.element, arguments );
			}
		},

		_always: function() {
			this.submit.button('reset');
			if ( $.isFunction( this.options.always ) ) {
				this.options.always.apply( this.element, arguments );
			}
		},

		// Change option values
		_setOption: function( key, value ) {
			switch( key ) {
			}

			$.Widget.prototype._setOption.apply( this, arguments );
		},

		// Cleanup
		destroy: function() {
			element.off('submit.ajaxform');
			$.Widget.prototype.destroy.call( this );
		}
	});
}( jQuery ) );