#!/bin/bash
rm -f *.zip *.tar.gz
tar -czf com_magebridge_installer_j25.tar.gz com_magebridge_installer
zip -rq9 com_magebridge_installer_j25.zip com_magebridge_installer
