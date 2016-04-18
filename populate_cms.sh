#!/bin/bash

set -e

echo "Removing earlier tar file of wordpress ...";
if [ -f wordpress-latest.tar.gz ]; then
	rm wordpress-latest.tar.gz
fi
echo "Downloading latest version of wordpress ..."
wget -O wordpress-latest.tar.gz http://wordpress.org/latest.tar.gz
echo "Unpacking latest version of wordpress ..."
tar xzf wordpress-latest.tar.gz
echo "Wordpress DONE!"

echo ""

echo "Removing earlier zip file of craft ..."
if [ -f craft-latest.zip ]; then
	rm craft-latest.zip
fi
echo "Downloading latest version of craft ..."
wget -O craft-latest.zip http://craftcms.com/latest.zip?accept_license=yes
echo "Unpacking latest version of craft ..."
unzip -o -q craft-latest.zip -d craft
echo "Craft DONE!"
