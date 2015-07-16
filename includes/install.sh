#!/bin/bash

echo "installing FruityProxy..."

apt-get -y install python-pip
apt-get -y install build-essential python-dev libffi-dev libssl-dev libxml2-dev libxslt1-dev
apt-get -y install python-setuptools --reinstall

pip install --upgrade setuptools

pip install --upgrade six

#https://pypi.python.org/packages/source/u/urwid/urwid-1.3.0.tar.gz # if error, install urwid manually first. (http://urwid.org/)

pip install --upgrade pyOpenSSL

pip install python-flask

pip install mitmproxy

pip install mitmflib

#IMAGES (upsidedown)
apt-get -y install libjpeg-dev
pip install --upgrade Pillow

wget https://github.com/xtr4nge/FruityProxy/archive/master.zip -O FruityProxy-master.zip
unzip FruityProxy-master.zip

chmod 755 ./fruityproxy.sh

ln -s /usr/share/fruitywifi/www/modules/bdfproxy/includes/bdf-proxy/bdf/ /usr/share/fruitywifi/www/modules/fruityproxy/includes/FruityProxy-master/modules/bdf

echo "..DONE.."
exit
