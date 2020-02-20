=== My WP Customize Admin/Frontend ===
Contributors: gqevu6bsiz
Donate link: http://gqevu6bsiz.chicappa.jp
Tags: admin, backend, frontend, customize, dashboard, debug, white label, sidebar, toolbar, metabox, posts, edit-post
Requires at least: 4.7
Tested up to: 5.3
Stable tag: 1.12.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simply and easy-to-use the customize for Admin and Frontend.

== Description ==

A lot of custom filters and actions, and included the developer tools.

= Easy setting screen =
Easily customize based on check boxes and text boxes.

= Many customization =
Freely editable admin sidebar, Admin footer text, Dashboard, Disable Author archive, etc ... and more customize. [My WP Customize](https://mywpcustomize.com)

= Custom actions and filters =
There are lots of custom actions and filters.

= Developer tools =
You will speed up to your site creation. Debug on current post, Debug on current using theme, Debug on server info, ...etc more helpful info.

== Installation ==

1. Upload the entire mywp folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You will find 'My WP' menu in your WordPress admin panel.

== Screenshots ==

1. Admin customize settings panel.
2. Frontend customize settings palel.
3. Debug settings panel.
4. Advanced customize settings view.
5. Show debug screen on bottom of page.

== Changelog ==

= 1.12.2 2019-11-13 =
* Fixed: Remove menu item on toolbar customize.
* Updated: Some small usability.

= 1.12.1 2019-09-19 =
* Added: Inlucde css&js for frontend.
* Fixed: Remove tags for input css fields.
* Fixed: Post per page setting on Posts customize.

= 1.12 2019-06-13 =
* Added: Redirect after login.
* Added: [mywp_url] to post permalink on shortcode.
* Updated: Remove cache for Sidebar and Toolbar.
* Fixed: Spaces on sidebar customized when screen zoom.
* Fixed: Login title filter for WP 5.2.
* Fixed: Change post type and taxonomy when slow network.

= 1.11 2019-03-20 =
* Added: Login form customize.
* Updated: Some Frontend debug.
* Updated: Some network admin debug.

= 1.10.2 2019-03-06 =
* Added: Manager Ajax and Network Manager Ajax actions.
* Added: Remove transients timing to Sidebar and Toolbar.
* Added: Automatic output of column body for Posts.
* Added: Added some features for Frontend.
* Fixed: Toolbar custom URL to attributes.
* Fixed: Some bugs.

= 1.10.1 2019-01-14 =
* Fixed: Sidebar custom UI.
* Fixed: Global $post on Media Libraries.
* Fixed: abstract setting module function name.
* Updated: Remove setting with registered list columns.

= 1.10 2019-01-08 =
* Added: Customize Comments columns.
* Added: Customize Uploads columns.
* Added: Customize Users columns.
* Updated: To improve the convenience of plugin.
* Fixed: Some bugs.

= 1.9 2018-12-29 =
* Added: Customize posts columns.
* Updated: Thirdparty.

= 1.8 2018-12-08 =
* Fixed: Some bugs.
* Added: Change the editor for post edit(block/classic).
* Added: Hide the meta boxes on post edit screen for Block Editor(WP ver5).

= 1.7.2 2018-11-09 =
* Fixed: Empty check for function to value(for PHP5.4).
* Updated: Check to image file URL.
* Added: Debug for $_COOKIE.

= 1.7.1 2018-09-25 =
* Fixed: Some bugs/translations.

= 1.7 2018-09-05 =
* Fixed: Some bugs/translations.
* Updated: Blog ID/Site ID can use to shortcodes.
* Updated: Organizing hook names.
* Added: Object for transient.
* Added: Network manager.

= 1.6.2 2018-08-09 =
* Fixed: Translations.
* Fixed: Reorder menu items on Sidebar/Toolbar customize.
* Added: User first name/last name/display name on shortcode.

= 1.6.1 2018-07-20 =
* Fixed: Definition of variable.
* Updated: Translation file(pot).

= 1.6 2018-07-10 (Slightly larger update) =
* Enhancement: Some filters added to Model, Controller, Setting.
* Enhancement: Cache to Sidebar&Toolbar customize.
* Added: Support taxonomy to Sidebar&Toolbar customize.
* Added: Dynamic menu items on Toolbar with multisite.
* Added: Roughly get max allowed packet size(mysql).
* Added: Some debug lists on Site of Developer.
* Added: parse_url() on request of Developer.
* Added: Example debug panel of Developer.
* Added: Network admin url to Shortcode.
* Changed: Early get Toolbar items hook.
* Changed: Model class.
* Changed: Restlict customize sub items to Sidebar&Toolbar.
* Changed: Some properties on Thirdparty.
* Fixed: Show defines to individual site when multisite.
* Removed: get_plugin_data_by_name on MywpThirdparty.

= 1.5.8 2018-05-05 =
* Enhancement: Show the current queries on Debug tool.

= 1.5.7 2018-04-18 =
* Fixed: Customized sidebar collapse menu.
* Updated: Custom menu UI on sidebar customize.

= 1.5.6 2018-04-03 =
* Added: Some filters/actions.
* Fixed: Debug value options, site_options.

= 1.5.5 2018-03-23 =
* Fixed: Only main_query on Archive disable.
* Fixed: Debug object to options, site_options, find option.

= 1.5.4 2018-02-27 =
* Fixed: Echo array on find option.

= 1.5.3 2018-02-27 =
* Fixed: Code miss on Developer.
* Fixed: Do shortcode on admin footer text.
* Fixed: Strict comparison.
* Added: Debug for options, site_options, blogs, find option.

= 1.5.2 2018-01-22 =
* Fixed: Do shortcode for custom html of admin bar customize.
* Fixed: Apply some filters for custom footer text of admin general.
* Fixed: Show icon for using custom icon of sidebar customize.

= 1.5.1 2017-12-25 =
* Added: Debug to user meta on User Edit.
* Enhancement: Network menu on admin toolbar customize.
* Fixed: Initialized data fields of controller.

= 1.5 2017-12-08 =
* Added: Shortcode to [mywp_site].
* Added: Syntax Highlight setting on User Profile.
* Fixed: Get meta serialize of post type.

= 1.4 2017-10-30 =
* Added: Customize the users page.
* Added: Disable the user dashboard.
* Changed: Priority change the main and network on mywp_setting_menus.
* Enhancement: Show Terms and Taxonomies on single page of Developer.
* Removed: Function "deep_esc_html" on Helper.

= 1.3.3 2017-09-08 =
* Enhancement: Customize sidebar.

= 1.3.2 2017-09-03 =
* Added: Change the capability for create_posts.
* Enhancement: Developer.
* Updated: Model data parse.

= 1.3.1 2017-08-28 =
* Fixed: Setting screen for Network.
* Added: Update messages posts and edit post.
* Added: Debug for Network.

= 1.3 2017-08-23 =
* Enhancement: All transients, All Translations, All Crons of debug.
* Enhancement: Dates of Developer.
* Updated: Require WordPress version to 4.7.

= 1.2.3 2017-08-19 =
* Enhancement: Edit Post / Add Post.
* Enhancement: Developer.
* Fixed: Block not use admin for ajax.

= 1.2.2 2017-08-16 =
* Fixed: Hide add new of post edit.
* Updated: Some feature of Developer.

= 1.2.1 2017-08-07 =
* Fixed: Show the structure template on developer.
* Enhancement: Developer.

= 1.2.0 2017-07-25 =
* Updated: Show the structure template on developer.

= 1.1.6 2017-06-15 =
* Updated: Some bugs.
* Added: Some small feature.

= 1.1.5 2017-04-22 =
* Updated: Small changes.

= 1.1.4 2017-03-09 =
* Fixed: Debug time to timestart().
* Fixed: Debug information on Frontend.
* Fixed: User edit advanced settings.

= 1.1.3 2017-03-08 =
* Added: Debug time to timestart().
* Added: Site customize.
* Added: Compatible network settings.
* Fixed: Contollers cache.

= 1.1.2 2017-03-04 =
* Added: Customize UI of admin sidebar.
* Added: Using cache controllers/setting menus.
* Added: Not use admin panel on Admin General.
* Fixed: Debug footer html.
* Fixed: Miss name to default from initial on Admin Posts.

= 1.1.1 2017-02-24 =
* Fixed: sidebar of admin customize.

= 1.1 2017-02-24 =
* Added: sidebar of admin customize.
* Added: custom third party activate filter.
* Added: include js file of admin general.

= 1.0 2017-02-17 =
* Initial release.
