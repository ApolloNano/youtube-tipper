#!/bin/bash
cd "$(dirname "$0")"
if ps -aux | grep -v grep | grep yt.php >/dev/null
then
     echo "Process is running."
else
     echo "Process is not running."
     php yt.php >> log.txt 2>> log.txt &
fi
