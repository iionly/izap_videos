iZAP Videos plugin for Elgg 1.9 - revised edition by iionly
Latest Version: 1.9.2
Released: 2014-11-03
Contact: iionly@gmx.de
License: GNU General Public License version 2
Copyright: (C) iZAP Web Solutions 2008-2014 (Original developer) / (C) iionly 2014 (for this fork)


Video plugin for Elgg. Supports addition of both on-server and off-server videos.

This version of the iZAP Videos plugin is based on version 3.71b of the original iZAP Videos plugin for Elgg 1.7 developed and released by iZAP Web Solutions. This present version of the iZAP Videos plugin works independently of an external feed server. Compatibility with an original version 3.X of the official iZAP Videos plugin should exist. If switching from an original version 4.X of the official iZAP Videos plugin also works has NOT been tested. It might work or not. If the Widget Manager plugin is installed, a Latest videos plugin for the index page is available. With Elggx Fivestar plugin installed users can rate the videos.


There are three video options available in this present version of the iZAP Videos plugin:

1. Off-server videos

This option allows to embed videos for specific sites by providing the videos' url from the original site. This option is currently available for videos of

- Dailymotion.com,
- Vimeo.com and
- Youtube.com

For this option to work cURL has to be installed on the server to allow for connecting to the original site. The feed is not processed by any external feed server but still the original site the video is hosted on has of course to be contacted.


2. On-server videos

This option allows uploading of videos directly to the server the Elgg site is hosted on. There are some additional requirements to be able to use this video option:

- for Linux servers only,
- FFMPEG must be installed and working,
- PDO-sqlite php extension must be installed, enabled and working,
- Supported video formats: avi, flv, 3gp, mp4, wmv, mpg and mpeg (other formats might works, i.e. are supported by FFMPEG, but this hasn't been tested and therefore such other formats are currently declined on upload),
- Maximum file size for uploads possible depend on server settings of upload_max_filesize, post_max_size, max_input_time, max_execution_time, memory_limit and by how much webspace you can provide. For support of uploading of larger videos you have to increase the variables to suitable values,
- All uploaded video files are converted to flv format (not necessary for uploads of flv files). The video player included in the iZAP Videos plugin requires a Flash plugin to be installed on the client side browser to be able to view on-server videos,
- Conversion of uploaded videos is triggered by a cronjob and you must have configured the Elgg cronjob for the interval selected in iZAP Videos plugin settings.


3. Adding videos by Embed-code

Theoretically, this option allows embedding of videos from any site by providing a (working) embed code. But usage of this option is RISKY and the htmlawed plugin bundled with Elgg will therefore filter out any such embed code. If you would disable the htmlawed plugin, the embed-code video option will work. But I can't recommend doing this because it would allow for embedding any code - and possibly also malicious code. Therefore, I ask you to use this option only within a safe environment (with only trusted users on your Elgg site) where you would be able to disable the htmlawed plugin safely. Otherwise, it's better to keep the embed-code video option disabled.



Installation:

(0. If any older version of the iZAP Videos plugin is installed, disable it on your site and remove the old plugin folder from the mod directory,)
1. Copy the izap_videos folder in your mod directory,
2. Enable the iZAP Videos plugin on your site,
3. Check the plugin settings ("Administer" - "Utilities" - "iZAP Videos") and configure the settings according to your likings. If you want to allow for on-server videos you need to have the necessary additional requirements described above fulfilled.



Changelog:

1.9.2 (by iionly)

- Updated version 1.8.2 for Elgg 1.9,
- Minimum Elgg version required is now Elgg 1.9.5 because only with this Elgg version the fatal error on activating the iZAP Videos plugin if there had been an older version of the original iZAP Video plugin had been previously installed will no longer occur.


1.8.2 (by iionly)

- Video conversion no longer done by a cli php script but triggered only via a cronjob (because the cli php script might not work anymore on some servers due to changes introduced by patches for the "shellshock" bash bug),
- Don't check filesize of a video currently in queue for conversion if conversion has not yet started,
- Show correct summary line for river entries of comments on videos.


1.9.1 (by iionly)

- Updated version 1.8.1 for Elgg 1.9.


1.8.1 (by iionly)

- Fixed a previous "fix" with handling of deletion of videos within the delete method of the IzapVideos class to correctly call the parent's class (ElggFile) delete method.
- Made the lightbox popup in the river "nicer" by setting a max-width to not fill the whole width of the screen if not necessary.


1.9.0 (by iionly)

- Updated version 1.8.0 for Elgg 1.9.


1.8.0 (by iionly)

- Initial release of this version of the iZAP Videos plugin,
- Updated for Elgg 1.8 based on version 3.71b of the original iZAP Videos plugin for Elgg 1.7,
- Code cleaning, re-organization and fixing of bugs,
- Dropping dependency on external feed server,
- Adding / fixing support for off-server videos from Dailymotion.com, Vimeo.com and Youtube.com without the need of an external feed server,
- Adding support for Widget Manager plugin (if available),
- German translations added,
- Custom list view for videos to be displayed in search results,
- Preview images in river activity entries,
- Starting video played directly from activity river in lightbox popup.
