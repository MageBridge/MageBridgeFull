#!/bin/bash
remote=hifiathome:/home/hifi/public_html/ 
scp -r components/com_magebridge_importer/* $remote/components/com_magebridge_importer/
scp -r administrator/components/com_magebridge_importer/* $remote/administrator/components/com_magebridge_importer/
scp -r media/com_magebridge_importer/* $remote/media/com_magebridge_importer/
