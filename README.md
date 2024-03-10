iZAP Videos plugin for Elgg 4.3 and newer Elgg 4.X - revised edition by iionly
==============================================================================

Latest Version: 4.3.0  
Released: 2024-03-09  
Contact: iionly@gmx.de  
License: GNU General Public License version 2  
Copyright: (C) iZAP Web Solutions 2008 (Original developer) / (C) iionly 2014 (for this fork)


Description
-----------

Video plugin for Elgg. Supports addition of both on-server and off-server videos.

This version of the iZAP Videos plugin is based on version 3.71b of the original iZAP Videos plugin for Elgg 1.7 developed and released by iZAP Web Solutions. This present version of the iZAP Videos plugin works independently of an external feed server. Compatibility with an original version 3.X of the official iZAP Videos plugin should exist. If switching from an original version 4.X of the official iZAP Videos plugin also works has NOT been tested. It might work or not. If the Widget Manager plugin is installed, a Latest videos plugin for the index page is available. With Elggx Fivestar plugin installed users can rate the videos.

You need to have the curl, pdo and sqlite php extensions installed and working on your server.


There are three video options available in this present version of the iZAP Videos plugin:

1. Off-server videos: This option allows to embed videos for specific sites by providing the videos' url from the original site. For this option to work cURL has to be installed on the server to allow for connecting to the original site. The feed is not processed by any external feed server but still the original site the video is hosted on has of course to be contacted. This option is currently available for videos of
  - Dailymotion.com,
  - Vimeo.com and
  - Youtube.com (requires an Google API key for Youtube Data API v3; follow the link on the API key settings tab to learn how to create an API key; you need to create an API key without restrictions on IP address or domain usage; also make sure the Youtube Data API v3 usage is enabled for the API key you've created).

2. On-server videos: This option allows uploading of videos directly to the server the Elgg site is hosted on. There are some additional requirements to be able to use this video option:
  - for Linux servers only (it might work on Windows servers but this is untested),
  - FFMPEG must be installed and working,
  - PDO-sqlite php extension must be installed, enabled and working,
  - Supported video formats: avi, flv, 3gp, mp4, wmv, mpg and mpeg (other formats might works, i.e. are supported by FFMPEG, but this hasn't been tested and therefore such other formats are currently declined on upload),
  - Maximum file size for uploads possible depend on server settings of upload_max_filesize, post_max_size, max_input_time, max_execution_time, memory_limit and by how much webspace you can provide. For support of uploading of larger videos you have to increase the variables to suitable values,
  - All uploaded video files are converted to MP4 format with support of HTML5 video playback by Video.js. As long as the users have a moderately modern browser installed they should be able to view the videos (no Flash or other plugin required on client side),
  - Conversion of uploaded videos is triggered by a cronjob and you must have configured the Elgg cronjob for the interval selected in iZAP Videos plugin settings.

3. Adding videos by Embed-code: Theoretically, this option allows embedding of videos from any site by providing a (working) embed code. But usage of this option is RISKY and the htmlawed plugin bundled with Elgg will therefore filter out any such embed code. If you would disable the htmlawed plugin, the embed-code video option will work. But I can't recommend doing this because it would allow for embedding any code - and possibly also malicious code. Therefore, I ask you to use this option only within a safe environment (with only trusted users on your Elgg site) where you would be able to disable the htmlawed plugin safely. Otherwise, it's better to keep the embed-code video option disabled.


Installation
------------

1. Make sure that curl, pdo and sqlite PHP extensions are installed and working on your server,
2. If any older version of the iZAP Videos plugin is installed, disable it on your site and remove the old plugin folder from the mod directory,
3. Copy the izap_videos folder in your mod directory,
4. Enable the iZAP Videos plugin on your site,
5. Check the plugin settings ("Administer" - "Utilities" - "iZAP Videos") for a pending iZAP Videos-specific upgrade and configure the settings according to your likings. If you want to allow for on-server videos you need to have the necessary additional requirements described above fulfilled. For adding of Youtube off-server videos to work you need to register a Google API key.
