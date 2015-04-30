jQuery(document).ready(function($) {
	$('.mhs_seo_title').keyup(function(event) {
		mhs_seo_title_count($(this));
	});
	$('.mhs_seo_desc').keyup(function(event) {
		mhs_seo_desc_count($(this));
	});
	function mhs_seo_title_count(field) {
		var max = 59;
		var len = $(field).val().length;
		var char = max - len;
		var area = $(field).attr('id') + '_area';

		if (len > max) {
			$('#'+area).html('<span style="color: red;">' + len + ' characters</span>');
		} else {
			$('#'+area).html('<span style="color: green;">' + len + ' characters</span>');
		}
	}
	function mhs_seo_desc_count(field) {
		var max = 156;
		var len = $(field).val().length;
		var char = max - len;
		var area = $(field).attr('id') + '_area';

		if (len > max) {
			$('#'+area).html('<span style="color: red;">' + len + ' characters</span>');
		} else {
			$('#'+area).html('<span style="color: green;">' + len + ' characters</span>');
		}
	}

	if($('.mhs_seo_title').length) {
		$('.mhs_seo_title').each(function() {
			mhs_seo_title_count($(this));
		});
	}
	if($('.mhs_seo_desc').length) {
		$('.mhs_seo_desc').each(function() {
			mhs_seo_desc_count($(this));
		});
	}

});