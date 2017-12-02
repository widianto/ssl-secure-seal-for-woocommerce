jQuery(document).ready(function($){
	var b=$('<input/>').attr({
		type: "button",
		id: "upload_button",
		class: 'button',
		value: 'Select Image'
	});
	$(b).insertAfter("#sswc_image");
	
	var uploader;
	  
	$("#upload_button").click(function() {
		event.preventDefault();
		if ( uploader ) {
			uploader.open();
			return;
		}
		
		uploader = wp.media({
			title: 'Select SSL Seal Image',
			button: {
			  text: 'Select SSL Image'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected in the media uploader...
		uploader.on( 'select', function() {
			// Get media attachment details from the uploader state
			var attachment = uploader.state().get('selection').first().toJSON();
			$('#sswc_image').val(attachment.url);
		});
		uploader.open();
		
	});
});