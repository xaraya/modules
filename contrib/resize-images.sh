#!/bin/bash

#####################################################################
# Image resizing script
#
# Thumbnail images for mimetypes were created by scaling down the
# Xaraya mime module's images.  This was because the Xaraya image
# module uses a faulty resizing mechanism which causes transparent
# pixels to become black (ugly!).  (I am not sure whether it is an
# issue with Apache's GD module or with the way resizing is done in
# Xaraya's images module.)
#
# You can use this script on a *nix machine to resize additional
# images.
#
# Requirements:
#     ImageMagick (specifically, the 'convert' utility)
#
# Usage:
#     ./resize-images.sh <glob>
#
#     Where <glob> represents the files you want to resize.
#     For example, to resize all .png files in the current
#     directory, enter "./resize-images.sh *.png"
#
# Author: Curtis Farnham
# License: GPL (as if a script this simple deserves any licensing...)
#####################################################################

SIZE=16x16

for i in $*; do
	BASE=${i:0:$((${#i}-4))}
	EXT=${i:$((${#i}-3))}
	echo "Converting $i => $BASE-$SIZE.$EXT"
	convert $i -thumbnail $SIZE $BASE-$SIZE.$EXT
done

