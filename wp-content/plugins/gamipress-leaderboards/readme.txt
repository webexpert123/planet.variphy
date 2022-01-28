=== GamiPress - Leaderboards ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, point, achievement, rank, badge, award, reward, credit, engagement, ajax
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 1.3.6
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Add leaderboards to intensify the gamification of your site.

== Description ==

Leaderboards gives you the ability to easily create, configure and add leaderboards on your website.

Place any leaderboard anywhere, including in-line on any page or post, using a simple shortcode, or on any sidebar through a configurable widget.

Also, this add-on adds new features to extend and expand the functionality of GamiPress.

= Features =

* Create as many leaderboards as you like.
* Ability to configure the metrics by which users should be ranked (the user rank, the points types and/or the number of earned achievements).
* Filter the leaderboard by a set of predefined time periods (today, yesterday, current week/month/year and past week/month/year).
* Support for custom time periods to filter the leaderboard on a range of dates you want.
* Responsive leaderboards that will get adapted to any screen size.
* Ability to configure the display options for each single leaderboard.
* Drag and drop options to reorder the leaderboard columns.
* Displayed leaderboards can be filtered and sorted without refresh the page.
* Configurable lazy loading feature for large leaderboards.
* Ability to set up leaderboard results cache to improve loading time speed on large leaderboards.
* Ability to hide website administrators from the leaderboard.
* Block, shortcode and widget to place any leaderboard anywhere.
* Block, shortcode and widget to show user's position on a specific leaderboard.

== Installation ==

= From WordPress backend =

1. Navigate to Plugins -> Add new.
2. Click the button "Upload Plugin" next to "Add plugins" title.
3. Upload the downloaded zip file and activate it.

= Direct upload =

1. Upload the downloaded zip file into your `wp-content/plugins/` folder.
2. Unzip the uploaded zip file.
3. Navigate to Plugins menu on your WordPress admin area.
4. Activate this plugin.

== Frequently Asked Questions ==

== Changelog ==

= 1.3.6 =

* **Improvements**
* Prevent hidden elements if switching from a small screen to a big one.
* Ensure to do not stop link redirections if any of the columns has links.
* Style improvements to ensure the correct visibility of the columns.
* Improved selectors to be more precise in Javascript and CSS rules.

= 1.3.5 =

* **Improvements**
* Style improvements for the responsive leaderboards.

= 1.3.4 =

* **New Features**
* Make leaderboards responsive for small screens.
* Added a toggle feature for responsive leaderboards in small screens.
* Added the attribute "force_responsive" to the [gamipress_leaderboard] shortcode.
* Added the option "Force Responsive" to the GamiPress: Leaderboard block and widget.

= 1.3.3 =

* **Improvements**
* Added required parameters in the 'get_the_excerpt' filter to avoid compatibility issues.

= 1.3.2 =

* **Bug Fixes**
* Fixed pagination error when datatables is enabled.

= 1.3.1 =

* **Developer Notes**
* Added new filters to allow override the number of users and users per page settings.

= 1.3.0 =

* **Bug Fixes**
* Fixed some typos in some leaderboard messages.

= 1.2.9 =

* **Bug Fixes**
* Fixed pagination on single templates.
* **Developer Notes**
* Added a new filter to disable the add-on libraries.

= 1.2.8 =

* **Improvements**
* Improved support for plugins that extend the Leaderboard limit (like Group Leaderboard plugins).

= 1.2.7 =

* **Bug Fixes**
* Prevent warnings on leaderboards with pagination and empty ranks.

= 1.2.6 =

* **New Features**
* Added pagination support.
* Added new options to setup the leaderboard pagination.
* Added a new option to merge the avatar and name columns into one column.
* **Improvements**
* Prevent WordPress auto p on leaderboards single pages.
* Automatically refresh the leaderboard cache everytime its set up changes.
* Several performance improvements to the leaderboard query.
* **Developer Notes**
* Added the ability to turn the leaderboard into an array.
* Full rewrite of the leaderboard functionality to bring support to pagination.

= 1.2.5 =

* **Improvements**
* Style improvements.
* Updated deprecated jQuery functions.

= 1.2.4 =

* **Improvements**
* Update date range functions to follow GamiPress functions.

= 1.2.3 =

* **Bug Fixes**
* Fixed incorrect week detection on the first day of the week.

= 1.2.2 =

* **Improvements**
* Ensure to use the WordPress timezone configuration for the leaderboard period feature.

= 1.2.1 =

* **Developer Notes**
* Added several hooks to make the leaderboard table output more flexible.

= 1.2.0 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Make use of WordPress security functions for ajax requests.
