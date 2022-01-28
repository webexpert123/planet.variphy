=== BuddyPress Reactions and Status ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/
Tags: buddypress, status, icon
Requires at least: 3.0.1
Tested up to: 5.8.1
Stable tag: 1.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The BuddyPress Reactions and Status plugin lets user add reactions to buddypress activities and set icons to appear beside their username at profile page.

== Description ==

The BuddyPress Reactions and Status plugin lets user add reactions to buddypress activities and set icons to appear beside their username at profile page.

The plug-in comes with various inbuilt icons to let user set their status icons according to their mood. The plugin also lets users set their status update.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `buddypress-status.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= Does This plugin requires BuddyPress? =

Yes, It needs you to have BuddyPress installed and activated.

== Changelog ==
= 1.9.0 =
* Enhancement: #77 - Give support to upload custom icons of svg format
* Enhancement: #75 - Update png icons to svg icon
* Fix: UI issue with olympus, aardvark, kleo themes
* Fix: #62 - admin notices

= 1.8.0 =
* Fix: #61 Update single profile status icons box UI
* Fix: #14 - String translation
* Fix: #59 - keep a set of icons checked on fresh installation

= 1.7.0 =
* Fix: Backend UI update

= 1.6.0 =
* Fix: get_headers(): https:// wrapper is disabled in the server configuration by allow_url_fopen=0

= 1.5.0 =
* Fix: Licence Update

= 1.4.1 =
* Enhancement: Added features to upload custom icons
* Enhancement: Added features to add notifcation for reactions

= 1.4.0 =
* Fix: (#25)Fixed updating a status
* Fix: (#23) Fixed show changes of edit status without reloading the page

= 1.3.0 =
* Fix: Added Option to remove Status icon
* Fix: Updated plugin option and set them active by default
* Fix: Public message will not capture icon image.

= 1.2.1 =
* Fix: Removed Action for logout users

= 1.2.0 =
* Fix: Added admin notice for BuddyPress based on class

= 1.1.1 =

* Fix - Real time status update on adding status and setting as current.
* Fix - PHP notices and warnings.

= 1.1.0 =
* Enhancement - bp 4.3.0 compatibility.
* Enhancement - Added buddypress reactions settings.

= 1.0.0 =
* first version.
