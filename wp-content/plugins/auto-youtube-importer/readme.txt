=== Auto YouTube Importer ===
Contributors: secondlinethemes
Donate link: https://secondlinethemes.com/
Tags: YouTube, import, vlog, feed, video, feed, channel, channels, playlist, playlists, episodes, embed, videos, sync
Requires at least: 4.8
Tested up to: 6.1
Requires PHP: 7.1
Stable tag: 1.0.5
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple YouTube video importer plugin. Import YouTube videos automatically to your WordPress site.

== Description ==

Sync YouTube channels (or playlists) with your WordPress website. The YouTube Importer plugin helps to easily import YouTube videos into WordPress as posts. You can import your YouTube channel to the regular WordPress Posts or to a custom post type (with the Pro version).
The plugin can import all your YouTube videos to certain and works especially well with themes developed by [SecondLineThemes](https://secondlinethemes.com)

The plugin supports importing YouTube videos into existing custom post types, assign categories, import featured images and more. Additionally, the plugin enables continuous import or "Sync" of your YouTube channels, so every time you release a new YouTube video, it can be automatically imported to WordPress. You can also set multiple import schedules and import different YouTube videos from separate channels at the same time.

To use the plugin, simply run a new import under "Tools -> YouTube Importer" via the main sidebar that appears in your WordPress dashboard. Set the different options and if you need a continuous import process for future posts, make sure to hit that checkbox before running the import process.
You can disable a schedueld import at any time by simply deleting the import entry under the "Scheduled Imports" tab. 

== Pro Version ==
The Pro version can be found here - [https://secondlinethemes.com/wordpress-youtube-importer](https://secondlinethemes.com/wordpress-youtube-importer)
It includes:
* Unlimited scheduled imports for multiple YouTube Channels / YouTube Playlists.
* Import to any Custom Post Type or Custom Taxonomy.
* Import video player to custom fields.
* Import tags and categories from the feeds.
* Force a re-sync on all existing posts (to update data)
* Set a global featured image to all imported posts.
* Manual "Sync" button to sync on demand.

== About SecondLineThemes ==

SecondLineThemes is developing unique WordPress themes and plugins for Podcasters, Vloggers, creators, and more. To hear more about us please check our website:
[https://secondlinethemes.com](https://secondlinethemes.com)


== Installation ==

1. Install directly from your WordPress dashboard or upload the plugin files to the `/wp-content/plugins/youtube-importer` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Run a new import via the "Tools -> YouTube Importer" section in your WordPress admin panel.
4. If needed, add, edit, or delete any of the scheduled import processes.

== Frequently Asked Questions ==

= The import failed or takes too much time to process? =
You can run the improter multiple times, as it will never import the same post twice. Once all videos are imported, only future posts would be added, assuming you selected the continuous import option.

= What is an "API Key"? =
To import your videos from YouTube, you'll have to create a key and save it in the plugin's settings.
An "API Key" is like a password that allows you to search, process and import videos from YouTube.
It is required by YouTube/Google that you have a valid key before you can import any data from their platform.

= Do I need to add a YouTube API key? =
Yes, a valid YouTube API key is required, you can create a key on the Google Cloud Platform.

= Is there a limit on the number of imported videos =
Yes. Google limits your API key with a daily quota.
If you're trying to import a large channel with thousands of videos, you can ask Google for an increase -
[https://support.google.com/youtube/contact/yt_api_form](https://support.google.com/youtube/contact/yt_api_form)

= The import does not work for my YouTube feed =
First, make sure your server is up to date with the requirements - we recommend PHP 7.1 or above.
Second, feel free to contact us if you encounter any issues.

== Screenshots ==

1. Import your YouTube videos to WordPress based on multiple options.
2. Add multiple continuous import processes of separate YouTube channels / playlists.

== Changelog ==

= 1.0.5 =
* Fix: Issue with importing multiple channels/playlists.

= 1.0.4 =
* Added: Support for YouTube playlists.
* Security Fix: Added missing nonce.

= 1.0.3 =
* Added: Import tags from YouTube.
* Improved: Clickable links in content/description.

= 1.0.2 =
* Fix: Missing license key input form for Pro users.

= 1.0.1 =
* Update: Bump WP version compatibility.
* Fix: Conflicts with older bundled versions of the Action Scheduler.

= 1.0.0 =
* Initial Release.
