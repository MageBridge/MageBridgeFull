#!/bin/bash

target=$1
root=`dirname $0`

cd $root
case $target in
    'all') 
        phing build gitpull
        phing build
        ;;
    'joomla') 
        phing build_joomla
        phing gitcommit
        ;;
    'joomla_patch') 
        phing build_joomla_patch
        ;;
    'magento') 
        phing build_magento
        ;;
    'magento_patch') 
        phing build_magento_patch
        ;;
    'magento_installer') 
        phing build_magento_installer
        ;;
    *)
        echo ""
        echo "Usage:"
        echo " ./build.sh all               = Build all packages"
        echo " ./build.sh joomla            = Build all Joomla! packages"
        echo " ./build.sh magento           = Build all Magento packages"
        echo " ./build.sh magento_patch     = Build only Magento patch"
        echo " ./build.sh magento_installer = Build only Magento Installer"
        echo ""
        ;;
esac 
