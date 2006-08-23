#!/bin/sh

bin_dir=`dirname $0`
cd $bin_dir
cd ..

php -f daemon.php 

#sleep 2
#server_pid=`ps wux | grep 'php \-f server.php' | grep -v grep | awk '{ print $2; exit }'`
#echo $server_pid > server.pid
