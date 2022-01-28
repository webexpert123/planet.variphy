=== BuddyBoss Platform Pro ===
Contributors: buddyboss
Requires at least: 4.9.1
Tested up to: 5.8.2
Requires PHP: 5.6.20
Stable tag: 1.1.9
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

BuddyBoss Platform Pro adds premium features to BuddyBoss Platform.

= Documentation =

- [Tutorials](https://www.buddyboss.com/resources/docs/)
- [Roadmap](https://www.buddyboss.com/roadmap/)

== Requirements ==

To run BuddyBoss Platform Pro, we recommend your host supports:

* PHP version 7.2 or greater.
* MySQL version 5.6 or greater, or, MariaDB version 10.0 or greater.
* HTTPS support.

== Installation ==

1. Make sure you have 'BuddyBoss Platform' installed.
2. Then visit 'Plugins > Add New'
3. Click 'Upload Plugin'
4. Upload the file 'buddyboss-platform-pro.zip'
5. Activate 'BuddyBoss Platform Pro' from your Plugins page.

== Changelog ==

= 1.1.9 =
* Zoom - Fixed Gutenberg block issues on adding existing webinar

= 1.1.8 =
* Zoom - Fixed create meeting/webinar password validation issue when it doesn't match requirements from Zoom settings

= 1.1.7 =
* Zoom - Added support to Send emails in Batches in the Background to Group members for Meeting and Webinar notifications
* Zoom - Fixed meeting and webinar timeout issue in the group by updating Client WebSDK
* Member Access Controls - Fixed minor UI issue in profile when message access configured

= 1.1.6 =
* Groups - Fixed Access control members issue in Group invites screen
* Compatibility - Fixed PHP 8.0 compatibility issues

= 1.1.5 =
* Member Access Controls - Provided hooks to clear API cache

= 1.1.4 =
* Media - Provided 'Member Access Controls' settings to decide which members should have access to upload videos
* Zoom - Fixed issue to run CRON only when zoom enabled

= 1.1.3.2 =
* Groups - Fixed group 'Member Access Controls' issue in Send invite screen
* Compatibility - Fixed WordPress 8.0 compatibility issues
* Translations - Updated German (formal) language files

= 1.1.3.1 =
* Compatibility - Fixed groups access control compatibility issue with MemberPress plugin

= 1.1.3 =
* Zoom - Improved meeting and webinar security

= 1.1.2.1 =
* Zoom - Fixed meeting and webinar critical security issue

= 1.1.2 =
* Zoom - Fixed Recordings play issue in the popup
* Zoom - Fixed Recordings popup when meeting title is long
* Translations - Updated German (formal) language files
* Compatibility - Fixed translation issue with 'TranslatePress' plugin

= 1.1.1 =
* Improvements - Repositioned 'View Tutorial' buttons in the settings

= 1.1.0.2 =
* Activity - Fixed issue with Edit and Delete permission in REST API

= 1.1.0.1 =
* Messages - Removed Group Message overridden template

= 1.1.0 =
* Groups - Provided 'Member Access Controls' settings to decide which members should have access to create and join Social Groups
* Activity - Provided 'Member Access Controls' settings to decide which members should have access to create activity posts
* Media - Provided 'Member Access Controls' settings to decide which members should have access to upload photos and documents
* Connections - Provided 'Member Access Controls' settings to decide which members should have access to send connection requests to other members
* Messages - Provided 'Member Access Controls' settings to decide which members should have access to send messages to other members
* Zoom - Updated 'Zoom Web SDK' library to 1.9.0
* Zoom - Fixed issue with the Recurring Meeting start time in the email

= 1.0.9 =
* Zoom - Added support for Zoom Webinar in Gutenberg blocks
* Zoom - Added support for Zoom Webinar in Social Groups
* Zoom - Added option to setup Meeting and Webinar notifications in Social Groups

= 1.0.8 =
* Zoom - Added 'Private Meeting URLs' support
* Zoom - Fixed Recurring meeting delete issue in Social Groups
* Zoom - Fixed Weekly occurrence Recurring meeting edit screen issue

= 1.0.7 =
* Zoom - Improved logic in social groups to show upcoming meeting until meeting ends
* Zoom - Fixed in browser meeting invalid signature bug
* Zoom - Fixed recording popup dates group not in sync with dates dropdown
* Zoom - Fixed multi-site license key issue

= 1.0.6 =
* Zoom - Support for Sync Zoom Meeting in Gutenberg block
* Zoom - Fixed 'wp_date' function compatibility with wp version before 5.3.0
* Zoom - Fixed zoom meeting activity block layout issue in mobile view

= 1.0.5 =
* Zoom - Zoom Join Meeting 'In-Browser' Support in Gutenberg block
* Zoom - Zoom Join Meeting 'In-Browser' Support in social groups
* Zoom - Fixed Zoom meeting countdown layout and days count issue

= 1.0.4 =
* Zoom - Support for Zoom Recurring Meeting in Gutenberg block
* Zoom - Support for Zoom Recurring Meeting in social groups
* Zoom - Added 'delete meeting' support for Zoom Gutenberg block
* Zoom - Fixed Zoom Gutenberg block setting sync issues

= 1.0.3 =
* Zoom - Fixed Zoom Gutenberg block duplication issues
* Zoom - Fixed Zoom 'meeting details' popup layout
* Zoom - Improved Zoom meeting countdown responsive layout
* Compatibility: Fixed 'BuddyBoss Theme' updater conflict

= 1.0.2 =
* Zoom - New setting to hide meeting recording 'Download' and 'Copy Link' buttons
* Zoom - Fixed meeting 'View Invitation' date sync bug

= 1.0.1 =
* Zoom - Fixed RTL layouts for Zoom content when WordPress is set to RTL languages
* Zoom - Removed ability to 'duplicate' the Gutenberg block, to avoid creating duplicate meetings
* Zoom - Fixed issues with saving social groups from backend when Zoom is disabled in the group

= 1.0.0 =
* Initial Release
* Support for Zoom in Gutenberg blocks
* Support for Zoom in social groups

