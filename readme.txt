=== Shopp SEO ===
Contributors: mainehost, godthor
Tags: SEO, Shopp, WordPress SEO
Requires at least: 3.9
Tested up to: 4.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ties together the functionality of WordPress SEO by Yoast with Shopp.

== Description ==

Shopp SEO is developed to bridge the gap between Shopp and WordPress SEO by Yoast. WordPress SEO by Yoast does not integrate with Shopp fully and this plugin fixes that.

Shopp SEO will provide you with meta fields for products and categories within Shopp. Most of the functionality of those fields is handled by WordPress SEO by Yoast and this plugin just provides that data to WordPress SEO by Yoast.

This plugin does remove the native functionality of Shopp with regards to meta descriptions on categories. Shopp creates a meta description for categories based off the category description and disregards WordPress SEO by Yoast. This plugin fixes that so only one meta description is created on a category and that is the one setup by WordPress SEO by Yoast.

Lastly, this plugin adds in the ability for you to setup SEO titles and descriptions for Shopp areas you couldn't otherwise SEO, like the Shopp landing page, account page, collections and more.

**Plugin Requirements**

* Shopp v1.3.4+
* WordPress SEO by Yoast v1.6.1+

**Note:** It may work on earlier versions of those plugins but it has not been tested.

== Installation ==

1. Upload the shopp-seo-mhs folder to the /wp-content/plugins/ directory.
2. Activate the Shopp SEO plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin by going to the Shopp SEO menu that appears under the Settings menu.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Admin area for Shopp pages and collections.
2. SEO fields for Shopp categories and products.

== Changelog ==

= 1.0.2 =

* **Changes**

	* Titles are no longer cut off at 70 characters. You can go as long as you like with it.
	* The title and description field will just show you how many characters you've typed. If you've exceeded 70 characters on the title, or 160 on the description, the character count will show in red to let you know you've exceeded the recommended lengths.

= 1.0.1 =

* **Bug Fixes**

	* Fixed an issue where the plugin would not activate if WordPress SEO Premium was installed instead of the free version. It should now activate for either version of WordPress SEO.

= 1.0.0 =

* Initial release of the plugin.