#!/bin/bash

# Definitions
base=`pwd`
joomla25=${base}/joomla2
joomla30=${base}/joomla3
rsync="rsync -rLERuC "

# init
cd $joomla25

# component
paths="components/com_magebridge administrator/components/com_magebridge media/*magebridge*"
for path in $paths; do
    $rsync ./${path} ${joomla30}
done

# site modules
modules="block cart cms login menu newsletter products progress slider switcher widget"
for module in $modules; do
    $rsync ./modules/mod_magebridge_${module} ${joomla30}
done

# admin modules
modules="orders customers"
for module in $modules; do
    $rsync ./administrator/modules/mod_magebridge_${module} ${joomla30}
done
    
# plugins
plugins="authentication community content finder system magebridge magento search user"
for plugin in $plugins; do
    cd $joomla25/plugins/$plugin/
    $rsync ./magebridge* $joomla30/plugins/$plugin/
done

# newsletter plugins
mkdir -p $joomla30/plugins/magebridgenewsletter/
cd $joomla25/plugins/
$rsync magebridgenewsletter $joomla30/plugins/

# product plugins
mkdir -p $joomla30/plugins/magebridgeproduct/
cd $joomla25/plugins/
$rsync magebridgeproduct $joomla30/plugins/

# store plugins
mkdir -p $joomla30/plugins/magebridgestore/
cd $joomla25/plugins/
$rsync magebridgestore $joomla30/plugins/

# profile plugins
mkdir -p $joomla30/plugins/magebridgeprofile/
cd $joomla25/plugins/
$rsync magebridgeprofile $joomla30/plugins/

# other plugins
cd $joomla25/plugins/system
$rsync magebridge* $joomla30/plugins/system

# languages
cp $joomla25/language/en-GB/*magebridge* $joomla30/language/en-GB/
cp $joomla25/administrator/language/en-GB/*magebridge* $joomla30/administrator/language/en-GB/
cp $joomla25/administrator/language/en-GB/*yireo* $joomla30/administrator/language/en-GB/

# end
