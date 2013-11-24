#!/bin/bash

# JCE editor v2
version="2.0.0"
zip -r9 jce_magebridgelinks.zip jce_magebridgelinks_${version} -x "*/.svn/"

# Xmap plugin
version=`cat xmap/com_magebridge.xml|grep '<version>'|cut -d\> -f2|cut -d\< -f1`
zip -r9 xmap_com_magebridge_${version}.zip xmap/com_magebridge*

# Xmap 2.0 plugin
version=`cat xmap2/com_magebridge.xml|grep '<version>'|cut -d\> -f2|cut -d\< -f1`
zip -r9 xmap_com_magebridge_${version}.zip xmap2/com_magebridge*

# Other scripts
cd scripts
zip -r9 magebridge_hostcheck.zip magebridge_hostcheck.php  
zip -r9 magebridge_productsync.zip magebridge_productsync.php
