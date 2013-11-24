#!/bin/bash
#
# Script to set symbolic links from MageBridgeCore-repo to MageBridgeFull-repo
#

# Variables
destinationDir=`pwd`
sourceDir=`dirname $destinationDir`/MageBridgeCore
symlinkTxt=symlink.txt

# Check for the symlink-file
if [ ! -f $symlinkTxt ] ; then
    echo "ERROR: Symlink-file not found"
fi

# Check for the symlink-file
if [ ! -f $symlinkTxt ] ; then
    echo "ERROR: Symlink-file not found"
fi

# Loop through the symlink file
cat symlink.txt|while read FILE; do

    if [ -d $sourceDir/$FILE ] ; then
        if [ -h $destinationDir/$FILE ] ; then
            echo "NOTICE: $FILE already exists"
            continue;
        elif [ -f $destinationDir/$FILE ]; then
            echo "WARNING: $FILE is a file"
        else
            echo "Linking $FILE"
            mkdir -p `dirname $FILE`
            ln -s $sourceDir/$FILE $destinationDir/$FILE
        fi
    fi

    if [ -f $sourceDir/$FILE ] ; then
        if [ -h $destinationDir/$FILE ] ; then
            echo "NOTICE: $FILE already exists"
            continue;
        else
            echo "Linking $FILE"
            mkdir -p `dirname $FILE`
            ln -s $sourceDir/$FILE $destinationDir/$FILE
        fi
    fi
done
