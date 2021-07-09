# youtube-tipper
Application that automatically sends a small amount of NANO to every YouTube comment containing a NANO address

# Requirements
PHP

MySQL

NANO node

Google YouTube API Key

# Installation
Rename /inc/config.sample.php to /inc/config.php

Import /db/youtube_tips.sql to your MySQL database

Update the config.php with your settings.

Set the Video ID in yt.php to the Video ID that you want the app to to tip on

Run the application either with php yt.php or with ./start.sh

Set an automatic running of the app in intervals with "crontab -e" or with the Windows Task Scheduler.
