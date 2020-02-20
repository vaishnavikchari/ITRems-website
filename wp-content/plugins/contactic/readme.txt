=== Contact Form 7 Database + | CFDB+ ===
Contributors: contactic,mouveo,francois86
Tags: CF7, contact form 7, CFDB, contact form, database, contact form database, save contact form, form database, contactic, form, forms, form builder, contact form, feedback, custom form, contact, web contact form, email, form manager, survey
Requires at least: 4.0
Tested up to: 5.2
Requires PHP: 5.4.45
Stable tag: 1.4.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Saves all your messages from Contact Form 7... Export data to a file or use shortcodes to display them. Get statistics as well as track your contacts.

== Description ==
### CONTACT FORM 7 DATABASE + : BACKUP & STATS

Contactic save form submissions to the database from severals contact form plugins and themes. 
Includes exports (Excel, CSV, HTML, JSON, Google Spreadsheet...), shortcodes (datatable, value, count, html...), tracking (source, status, usefulness...) and stats.

You can export data to a file (CSV, XLS, Google Sheet, JSON, ODS...) or even display them online.
You can track your contacts and know if they are useful or not, handle or not...
You can see quickly your progress thanks to statistical graphs.
More than 50 features are included in Contactic!

### New!
You can receive on Slack a notification as soon as you get a submission on your website

> With CFDB+, secure your forms and get stats, quickly and easily!

Contact Form 7 Database + is a fork of the old plugin CFDB (contact-form-7-to-database-extension). If you have this old plugin, update quickly and easily!

By simply installing the plugin, it will automatically begin to capture form submissions from (and more):

* Contact Form 7 (CF7) plugin
* JetPack Contact Form plugin
* Gravity Forms plugin
* Form Maker plugin
* Formidable Forms (BETA)
* Quform plugin (BETA)
* Ninja Forms plugin (BETA)
* Caldera Forms plugin (BETA)
* CFormsII (BETA)
* FormCraft Premium (BETA)
* Enfold theme forms

Other form submissions can be saved with the addition of the [cfdb-save-form-post] shortcode on the target submission page.

### SUPERCHARGE YOUR CONTACT FORM PLUGIN

Contact form plugins are great except for one thing... the ability to save and retrieve the form data to/from the database.

If you get a lot of form submissions, then you end up sorting through a lot of email.

#### ADMINISTRATION

This plugin provides four administration pages in the administration area under the **Contactic** submenu.

* **OVERVIEW** to view and export 100 last form submission data (all forms)
* **CONTACTS** to view and export form submission data (by form)
* **SHORTCODES** to generate shortcodes and exports
* **OPTIONS** to change configuration parameters
* **WEBHOOKS** Add your Slack Webhook Url to get notifyed by a message in your prefered channel, when someone submits a form. Once your webhook url save you will be able to create slack bots. (More to come...)

##### DISPLAYING SAVED DATA IN POSTS AND PAGES

Use shortcodes such as [cfdb-html], [cfdb-table], [cfdb-datatable], [cfdb-value] and [cfdb-json] to display the data on a non-admin page on your site.
Use the short code builder page to set shortcode options.

###EXTRAS###
More info on our website [Contactic.io](https://contactic.io/)
Documentation, howto and faqs [Contactic Documentation](https://contactic.io/docs/) (in progress)
Follow us and get exclusive news on [Twitter](https://twitter.com/Contactic_io)

== Installation ==
#### FROM WITHIN WORDPRESS
Visit ‘Plugins > Add New’
Search for 'Contactic +'
Activate 'Contact Form 7 Database +' from your Plugins page.
Go to "after activation" below.

#### MANUALLY
Upload the wordpress folder to the /wp-content/plugins/ directory
Activate the 'Contact Form 7 Database +' plugin through the 'Plugins' menu in WordPress
Go to “after activation” below.

#### AFTER ACTIVATION
Nothing to do if you just want to save contacts form submissions.
If you want to setup, go to 'OPTIONS' page in the Administration.
Enjoy!

== Frequently Asked Questions ==
##### Is there a tutorial?
Refer the [Plugin Documentation Site](https://contactic.io/docs) (in progress)

##### I installed the plugin but I don't see any of my forms listed in the administration page
Nothing will show until you have actual form submissions captured by this plugin. The plugin is not aware of your form definitions, it is only aware of form submissions.

##### Where can I find documentation on the plugin?
Refer the [Plugin Documentation Site](https://contactic.io/docs) (in progress)

##### Where do I see the data?
In the admin page, "Contacts"

##### Can I display form data on a non-admin web page or in a post?
Yes, go to [Plugin Documentation Site](https://contactic.io/docs)  on shortcodes `[cfdb-html]`, `[cfdb-datatable]`, `[cfdb-table]`, `[cfdb-json]` and `[cfdb-value]`, etc.

##### What is the name of the table where the data is stored?
`wp_contactic_submits`
> Note: if you changed your WordPress MySql table prefix from the default `wp_` to something else, then this table will also have that prefix instead of `wp_` (`$wpdb->prefix`)

##### If I uninstall the plugin, what happens to its data in the database?
By default it remains in your database in its own table.
There is an option to have the plugin delete all its data if you uninstall it that you can set if you like.
You can always deactivate the plugin without loosing data.

== Screenshots ==
1. Overview
2. Contacts
3. Shortcodes
4. Options
5. Webhooks

== Upgrade Notice ==
If you use an old version of CFDB, you will have stats, better UI and tools to track all your contacts form submissions.

== Changelog ==

= 1.4.0 =
* Make source tracking (so the front admin-ajax call) optional in options
* Add form title to slack bot notifications
* Fix order bug in transform shortcodes builder

= 1.3.7 =
* Add form data to slack bot notifications

= 1.3.6 =
* Fix security settings roles

= 1.3.5 =
* Save state of datatables on contacts page
* Hide blocked fields (NoSaveFields option) on contacts page

= 1.3.4 =
* Minor bugfix

= 1.3.3 =
* Improve french translation
* Improve options design
* Add Spanish translation

= 1.3.2 =
* Fix enfold form custom html elements labels matching bug

= 1.3.1 =
* Fix 1.3.0 release error missing files

= 1.3.0 =
* New : Add your Slack Webhook Url to get notifyed by a message in your prefered channel, when someone submits a form. Once your webhook url save you will be able to create slack bots. 
* Fix a bug when using CTC_FormIterator class.

= 1.2.2 =
* New : source can now be pushed in cf7 email.
* Tested every feature, our plugin is Wordpress 5.1 compatible.

= 1.2.1 =
* Fix : missing js asset.

= 1.2.0 =
* New : get the source (origin referer) and submit page even on cached pages.
* Added source (origin referer) and submit page in details modal.

= 1.1.0 =
* Support session-less php servers configurations.
* Custom admin footer in our plugin pages.

= 1.0.7 =
* Fix submit details modal display issue on mobile in overview and contacts pages.
* Added date range filtering in contacts page.
* Remove old options that are now useless

= 1.0.6 =
* Handle multiple emails forms fields to display and dedup in overview page.
* Added a date range picker in overview page.

= 1.0.5 =
* Fix duplicate email display that may occur in overview page.

= 1.0.4 =
* Limit visible page title length in overview column and bugfix.

= 1.0.3 =
* Saving page title and uri in submitted form data.

= 1.0.2 =
* Saving page ID in submitted form data instead of splitting form name.
 
= 1.0.1 =
* Added a checkbox option to merge same forms in shortcode/export builder.
 
= 1.0 =
* Stats
* UI
* Security