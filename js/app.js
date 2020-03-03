var UPLOADER = {
	copyUrl: function(id) {
		var ci_id = 'url' + id,
			co_id = 'copy' + id,
			CI = document.getElementById(ci_id),
			OK = document.getElementById(co_id);
		CI.select();
		CI.setSelectionRange(0, 99999);
		document.execCommand("copy");
		OK.style.display = 'block';
	},
	count: 0,
	init: function(){
		jQuery('#fileupload').fileupload({url: server_url,autoUpload:1,dropZone:jQuery(document)});
		jQuery('#fileupload')
			.addClass('fileupload-processing')
			.bind('fileuploaddone', function (e, data) {
				UPLOADER.count++;
				jQuery('#linkgen').show();
			})
			.bind('fileuploaddestroyed', function (e, data){
				UPLOADER.count--;
				if (UPLOADER.count == 0) {
					jQuery('#linkgen').hide();
				}
			});
		jQuery.ajax({
			url: jQuery('#fileupload').fileupload('option', 'url'),
			dataType: 'json',
			context: jQuery('#fileupload')[0]
		}).always(function(a,b,c) {
			UPLOADER.count = a.files.length;
			if (a.files.length) {
				jQuery('#linkgen').show()
			} else {
				jQuery('#linkgen').hide();
			}
			jQuery(this).removeClass('fileupload-processing');
		}).done(function(result) {
			jQuery(this).fileupload('option', 'done').call(this, jQuery.Event('done'), { result: result });
		});
		jQuery(document).bind('drop dragover', function (e) {
			e.preventDefault();
		});
	}
}