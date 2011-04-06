#!/bin/bash

# /* Copyright 2008 Timothy White */

# Mirrors common apps so that all users can download them from local mirror. Not all apps are automatically the latest version, some are "hard coded"

# Download firefox "all" page
QUIET="-nv --trust-server-names=on"

wget -q http://www.mozilla.com/en-US/firefox/all.html -O /tmp/firefox.downloads
wget -q http://www.mozillamessaging.com/en-US/thunderbird/all.html -O /tmp/thunderbird.downloads
winen=$(cat /tmp/firefox.downloads | grep lang=en-GB |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
macen=$(cat /tmp/firefox.downloads | grep lang=en-GB |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
#winaf=$(cat /tmp/firefox.downloads | grep lang=af |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
#macaf=$(cat /tmp/firefox.downloads | grep lang=af |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
cd /usr/share/grase/www/public/Firefox/
wget -c $QUIET "$winen" "$macen"
# "$winaf" "$macaf"
winen=$(cat /tmp/thunderbird.downloads | grep lang=en-GB |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
macen=$(cat /tmp/thunderbird.downloads | grep lang=en-GB |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
#winaf=$(cat /tmp/thunderbird.downloads | grep lang=af |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
#macaf=$(cat /tmp/thunderbird.downloads | grep lang=af |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
cd /usr/share/grase/www/public/Thunderbird
wget -c $QUIET "$winen" "$macen"
# "$winaf" "$macaf"

# Opera Browser

cd /usr/share/grase/www/public/Opera/Mac
wget -c $QUIET http://mirror.internode.on.net/pub/opera/mac/1063/Opera_10.63_Setup.dmg
wget -c $QUIET  http://mirror.internode.on.net/pub/opera/mac/1063/Opera_10.63_Setup_Intel.dmg
cd /usr/share/grase/www/public/Opera/Windows
wget -c $QUIET  http://mirror.internode.on.net/pub/opera/win/1063/en/Opera_1063_en_Setup.exe

# Chrome


# Safari

# Avast
cd /usr/share/grase/www/public/Avast
if [ ! -s setupeng.exe ]; then
wget -c $QUIET  http://files.avast.com/iavs4pro/setupeng.exe
fi
