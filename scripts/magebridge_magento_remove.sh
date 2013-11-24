#!/bin/bash

paths=$(cat <<EOF
app/etc/modules/Yireo_MageBridge.xml
app/etc/modules/Jira_MageBridge.xml
app/code/community/Yireo/MageBridge*
app/code/community/Jira/MageBridge*
app/design/frontend/default/default/layout/magebridge.xml
app/design/frontend/default/default/template/magebridge
app/design/frontend/default/magebridge
app/design/adminhtml/default/default/layout/magebridge.xml
app/design/adminhtml/default/default/template/magebridge
skin/adminhtml/default/default/images/magebridge
magebridge.class.php
magebridge.php
var/connect/Yireo_MageBridge.xml
var/connect/Yireo_MageBridge-*
js/magebridge
var/log/magebridge.log
EOF
)

for path in $paths; do
    rm -rf $path
done
