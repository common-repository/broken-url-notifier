jQuery(document).ready(function(){
	var custom_uploader;
	var switches = document.querySelectorAll('input[type="checkbox"]');
	for (var i=0, sw; sw = switches[i++]; ) {
		var div = document.createElement('div');
		div.className = 'switch';
		sw.parentNode.insertBefore(div, sw.nextSibling);
	}
	
	jQuery('#upload_image_button').click(function(e) {
		var radioBtn = jQuery(this).parent().prev().attr('id');
		var clickedBTN = jQuery(this);
		jQuery('#'+radioBtn).click();
		e.preventDefault();
		if (custom_uploader) {  custom_uploader.open(); return; }
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {  text: 'Choose Image'  },
			multiple: false
		});
		custom_uploader.on('select', function() {
			attachment = custom_uploader.state().get('selection').first().toJSON();
			var thubmnail = attachment['sizes']['thumbnail'].url;
			jQuery('#'+radioBtn).val(attachment.url);
			clickedBTN.before('<img data-src="'+attachment.url+'" src="'+thubmnail+'" class="settings_view_image" /> ');
		});
		custom_uploader.open();
	});
	
    jQuery('button.issue_fixed').click(function(){
        var key = jQuery(this).attr('data-delete-key');
        jQuery(this).append('<span class="spinner is-active bun_myCustomSpinner"></span>');
		jQuery(this).attr('disabled','disable');
        jQuery.ajax({
            url:ajaxurl,
            data:'action=delete_log&key='+key,
            method:'post'
        }).done(function(msg){
            if(msg == 'done'){
                jQuery('tr#'+key).fadeOut('slow',function(){
                    jQuery(this).remove();
                })
            }
        });
    });	
	
	
	jQuery('select#filter_by_section').change(function(){
		var value = jQuery(this).val();
		if(value == ''){
			jQuery('table tbody tr').show();
		}else{
			jQuery('table tbody tr').hide();
			jQuery('table tbody tr[data-type="'+value+'" ]').show();
		}
		
		
		
	});
	
	
});