jQuery(document).ready(function($) {
	$('.mhs_seo_title').keyup(function(event) {
		mhs_seo_title_count($(this));
	});
	$('.mhs_seo_desc').keyup(function(event) {
		mhs_seo_desc_count($(this));
	});
	function mhs_seo_title_count(field) {
		var max = 70;
		var len = $(field).val().length;
		var char = max - len;
		var area = $(field).attr('id') + '_area';

		if (len >= max) {
			$('#'+area).html('<span style="color: red;">-' + char + ' characters left</span>');
		} else {
			$('#'+area).html('<span style="color: green;">' + char + ' characters left</span>');
		}
	}
	function mhs_seo_desc_count(field) {
		var max = 160;
		var len = $(field).val().length;
		var char = max - len;
		var area = $(field).attr('id') + '_area';

		if (len >= max) {
			$('#'+area).html('<span style="color: red;">-' + char + ' characters left</span>');
		} else {
			$('#'+area).html('<span style="color: green;">' + char + ' characters left</span>');
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