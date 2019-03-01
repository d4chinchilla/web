#!/bin/bash

fname="/var/www/html/chinchilla-reset"
logfile="/tmp/chinchilla-log"

[[ -p $fname ]] || mkfifo $fname
[[ -f $logfile ]] && rm $logfile

shutdown()
{
    echo SHUTTING DOWN!
    # Here, put code to stop all current processes
}

start()
{
    echo STARTING!
    # Here, put code to start a new set of processes
}

install()
{
    echo INSTALLING!
    # Here, put code to verify an install file and install it
    lxterminal -e echo Hello!
    if gpg -o firmware.zip -d firmware.zip.gpg; then
        unzip firmware.zip -d fwExtract
        find /var/www/html/fwExtract -iname '*.bin' -exec cp {} /media/pi/NODE_L432KC \;
    fi
}

while true; do
    if read -r line < $fname; then
        echo $line
        case $line in
        restart)
            shutdown
            start
            ;;
        stop)
            shutdown
            ;;
        start)
            start
            ;;
        install)
            shutdown
            install
            start
            ;;
        esac
    else
        echo Sleepy
        sleep 1
    fi >> $logfile
done
