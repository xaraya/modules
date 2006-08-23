#!/bin/sh

bin_dir=`dirname $0`
cd $bin_dir
cd ..

php -f client/shell.php
