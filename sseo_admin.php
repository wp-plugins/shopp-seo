<?php

if(!class_exists('shopp_seo_mhs_admin')) {
	class shopp_seo_mhs_admin extends shopp_seo_mhs {
		protected $ssmhs_folder = '';

		function __construct() {
		}
		function set_vars() {
			$this->ssmhs_folder = plugin_basename(dirname(__FILE__));
		}
		function landing() {
			$image_path = plugins_url('images/', __FILE__);

			$code = '
				<h1>' . SSEO_NAME . '</h1>
				<div>
					<div style="float: left; width: 73%; margin-right: 2%;">
						Shopp SEO uses the variables offered by ' . SSEO_WP_SEO_NAME . ' but there are limitations on the ' . SSEO_WP_SEO_NAME . ' variables you can use. The following variables are available for use below in the settings:
						<p>
						%%sitename%%<br />
						%%sitedesc%%<br />
						%%sep%%<br />
						%%pagenumber%%
						</p>
						<p>
						<span style="font-weight: bold;">Note on Collections:</span> A collection is created with a ' . SSEO_SHOPP_NAME . ' shortcode placed on a page. The SEO for that page where the shortcode is placed will be handled by ' . SSEO_WP_SEO_NAME . '. However, if there are additional pages for that collection then those additional pages will be handled by ' . SSEO_NAME . ' using the settings below.
						</p>
					</div>
					<div style="float: right; width: 22%; border-left: 1px solid #c5c5c5; padding-left: 2%;">
						<a href="https://wordpress.org/plugins/seo-enforcer/" target="_blank"><img src="' . $image_path . 'seoe_icon.png" style="max-width: 100%;"></a>
						<p>
						Get even more control of SEO with <a href="https://wordpress.org/plugins/seo-enforcer/" target="_blank">SEO Enforcer</a>. Works great in combination with Shopp SEO.						
						</p>
					</div>
				</div>
				<br style="clear: both;" />
				<form method="post" action="?page=' . $this->ssmhs_folder . '">
				<input type="hidden" name="mode" value="collection">';

			foreach($this->meta_area as $key=>$value) {
				$title = sprintf($this->seo_title, $value);
				$desc = sprintf($this->seo_desc, $value);
				$noindex = sprintf($this->seo_noindex, $value);

				$code .= '
					<div style="float: left; width: 48%; margin-right: 2%;">
						<h3>' . $key . '</h3>
						<p>
						' . $this->meta_area_desc[$value] . '
						</p>
						<span style="font-weight: bold;">Title:</span><br />
						<input type="text" name="' . $title . '" id="' . $title . '" size="40" style="width: 100%;" value="' . get_option($title) . '" class="mhs_seo_title">
						<div id="' . $title . '_area" style="margin: 0; padding: 0;"></div><br />
						<span style="font-weight: bold;">Description:</span><br />
						<textarea name="' . $desc . '" id="' . $desc . '" rows="5" cols="40" style="width: 100%;" class="mhs_seo_desc" wrap>' . get_option($desc) . '</textarea>
						<div id="' . $desc . '_area"></div><br />
						<span style="font-weight: bold;">Meta Robots:</span> <input type="radio" name="' . $noindex . '" value="1"' . ((get_option($noindex)) ? ' checked' : '') . '> noindex, follow <input type="radio" name="' . $noindex . '" value="0"' . ((!get_option($noindex)) ? ' checked' : '') . '> index, follow
						<div style="border-bottom: 1px solid black; margin: 5px 0 5px 0; width: 100%"></div>
					</div>';	
			}

			$code .= '<p style="clear: both; margin-top: 15px;" /><input type="submit" value="Update" class="button button-primary"></form></p>';

			return $code;		
		}
	}
}

$ssmhs_admin = new shopp_seo_mhs_admin();
$ssmhs_admin->set_vars();

switch($mode) {
	default:
	case 'collection':
		if($_POST) {
			foreach($_POST as $key=>$value) {
				if($key != 'mode' && $key != 'submit') {
					update_option($key, $value);
				}
			}
		}

		echo $ssmhs_admin->landing();
	break;
}

?>