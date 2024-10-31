=== Rotating Banners ===
Contributors: mFlorin
Tags: rotate, rotating, banner, banners, header, headers, ajax, widget
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: 2.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create Sections of your website that change dynamically each minute/hour/day/month you set.

== Description ==

With this plugin you can put a shortcode somewhere in your website (in header, sidebar, posts or pages, anywhere)
and then you can add Rotating Banners.
In Frontsite (where you put your shortcode) the Banners will show.
After that, you can customize the time banners change (seconds/minutes/hours/days/months).
After the specified time has passed, in frontend (where you put your shortcode) the next banner will show up.

When you add a new Rotating Banner, these are the options:

- First, the title: A simple text for you to know which is which.
- Second, the Switcher, where you have 2 options: Simple or Advanced.

- Option 1: Simple Mode.
When this option is activated, you have access to the WordPress Editor to customize your Banner.
- Option 2: Advanced Mode.
When this option is activated, the WordPress Editor will be hidden, and instead, you have 3 new Options
HTML: The HTML that will show up where you put your shortcode.
CSS: The Style for your HTML.
JS: If you need JS code for your HTML, put it here.

Then, you can go to Rotating Banners -> Group.
From there you can copy the shortcode to install in your site,
and also you can customize different settings of the Group (change the order of the Rotating Banners, change the time they rotate, etc)

== Installation ==

How to install the plugin?

1. Install the plugin
2. Option 1: Upload the contents of `rotating-banners.zip` to the `/wp-content/plugins/` directory.
3. Option 2: From WordPress Backend, go to Plugins -> Add New, and search for `Rotating Banners`, then install from there.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Add Rotating Banners from WordPress Backend -> Rotating Banners -> Add Rotating Banner.
6. Modify Rotating Group from WordPress Backend -> Rotating Banners -> Group.
7. Copy the ShortCode from the Group page and paste it where you need it.

== Frequently Asked Questions ==

= What if something does not work right, I need support, I want to report a bug or just ask a question? =

Create a support Ticket by sending an email to support@marketinghack.fr

== Changelog ==

= 2.2.2 =
* Released: 10 March 2016
* Fix: Fixed the issue with not saving 'Group Config' if you have more than 20 Rotating Banners in the 'Available Rotating Banners' select2 column at Group Page.
* Change: Updated the multi language template file (.pot file)

= 2.2.1 =
* Released: 16 February 2016
* New: Added shortcode parameter `widget`. If you put your shortcode in a Widget, then you also need to put this parameter to the shortcode :: `widget='true'`. Ex: `[rotating_banners]` vs. `[rotating_banners widget='true']`.
* New: Added on Group Page a new area ( Shortcode Settings (Helper Buttons) ) with a button to Toggle the :: widget='true' :: to the shortcode copy buttons
* New: Changes to Group page CSS (removed the buttons shadow (box-shadow and text-shadow))

= 2.2.0 =
* Released: 12 February 2016
* New: Add Shortcode support on Text Widgets (Now the [rotating_banners] shortcode work with Text Widgets too. Can pe pasted into a Text Widget and be put in a Sidebar or Footer, and will work).
* Change: Updated the multi language template file (.pot file)

= 2.1.0 =
* Released: 30 December 2015
* New: Added an AJAX method that permits the Rotating Banners to still be changed even if the page is static (by Cache Plugins or something like that).

= 2.0.2 =
* Released: 23 November 2015
* New: Added the full .pot template file (for multi-language)
* New: Added a text to the License page with the URL where you can upgrade to the Pro Version which offers the ability to have Multiple Groups.
* Change: Changed a bit the Licensing system. Now it shows some errors instead of just "Invalid License Key".
* Fix: Modified some texts across plugin pages.

= 2.0.1 =
* Released: 18 November 2015
* Fix: Added more security to the plugin's files (css/js/views) to not be accessible outside plugin.
* Fix: When plugin is deleted/uninstalled, if Pro version is installed too, it won't delete the database entries/settings.

= 2.0.0 =
* Released: 05 November 2015
* The first version for WordPress Repository
* Complete rewrite of the plugin.

= 1.x =
* The old plugin...