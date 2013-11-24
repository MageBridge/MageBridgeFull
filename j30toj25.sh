#!/bin/bash

# Definitions
base=`pwd`
joomla25=${base}/joomla25
joomla30=${base}/joomla30
rsync="rsync -rERuC "

# init
cd $joomla30

# component
paths="components/com_magebridge administrator/components/com_magebridge media/com_magebridge"
for path in $paths; do
    $rsync ./${path} ${joomla25}
done

# site modules
modules="block cart cms login menu newsletter products progress roklogin rokscroller switcher"
for module in $modules; do
    $rsync ./modules/mod_magebridge_${module} ${joomla25}
done

# admin modules
modules="orders customers"
for module in $modules; do
    $rsync ./administrator/modules/mod_magebridge_${module} ${joomla25}
done
    
# plugins
plugins="authentication community content system magebridge magento search user"
for plugin in $plugins; do
    cd $joomla30/plugins/$plugin/
    $rsync ./magebridge* $joomla25/plugins/$plugin/
done

# newsletter plugins
mkdir -p $joomla25/plugins/magebridge.newsletter/
cd $joomla25/plugins/
$rsync magebridge.newsletter $joomla25/plugins/

# other plugins
cd $joomla25/plugins/system
$rsync magebridge* $joomla25/plugins/system

# languages
cp $joomla30/language/en-GB/*magebridge* $joomla25/language/en-GB/
cp $joomla30/administrator/language/en-GB/*magebridge* $joomla25/administrator/language/en-GB/

# end
