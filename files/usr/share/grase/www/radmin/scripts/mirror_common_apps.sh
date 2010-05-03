#!/bin/bash

# Mirrors common apps so that all users can download them from local mirror. Not all apps are automatically the latest version, some are "hard coded"

# Download firefox "all" page

wget -q http://www.mozilla.com/en-US/firefox/all.html -O /tmp/firefox.downloads
wget -q http://www.mozillamessaging.com/en-US/thunderbird/all.html -O /tmp/thunderbird.downloads
winen=$(cat /tmp/firefox.downloads | grep lang=en-GB |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
macen=$(cat /tmp/firefox.downloads | grep lang=en-GB |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
#winaf=$(cat /tmp/firefox.downloads | grep lang=af |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
#macaf=$(cat /tmp/firefox.downloads | grep lang=af |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
cd /var/www/public/Firefox/
wget -c -q "$winen" "$macen"
# "$winaf" "$macaf"
winen=$(cat /tmp/thunderbird.downloads | grep lang=en-GB |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
macen=$(cat /tmp/thunderbird.downloads | grep lang=en-GB |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
#winaf=$(cat /tmp/thunderbird.downloads | grep lang=af |grep download-windows |grep -o http[^\"]* |sed 's/amp;//g')
#macaf=$(cat /tmp/thunderbird.downloads | grep lang=af |grep download-osx-uni |grep -o http[^\"]* |sed 's/amp;//g')
cd /var/www/public/Thunderbird
wget -c -q "$winen" "$macen"
# "$winaf" "$macaf"

# Opera Browser

cd /var/www/public/Opera/Mac
wget -c -q http://mirror.internode.on.net/pub/opera/mac/1000b2/Opera_10.00_b2_Setup.dmg
wget -c -q http://mirror.internode.on.net/pub/opera/mac/1000b2/Opera_10.00_b2_Setup_Intel.dmg
cd /var/www/public/Opera/Windows
wget -c -q http://mirror.internode.on.net/pub/opera/win/1000b2/en/Opera_1000_en_b2_Setup.exe

# Chrome


# Safari

# Avast
cd /var/www/public/Avast
if [ ! -s setupeng.exe ]; then
wget -c -q http://files.avast.com/iavs4pro/setupeng.exe
fi
