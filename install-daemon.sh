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

    if ! gpg -o /var/www/html/uploads/firmware.zip -d /var/www/html/uploads/firmware.zip.gpg; then
	    echo Unzipping!
        unzip /var/www/html/uploads/firmware.zip -d /var/www/html/fwExtract 2>file
        echo Extracting!
        find /var/www/html/fwExtract -iname '*.bin' -exec cp {} /home/pi/D4 \;
    else
        echo Incorrectly signed file!
    fi
}

reset()
{
    echo RESTARTING PI!
    reboot
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
        reset)
            shutdown
            reset
            ;;

        esac
    else
        echo Sleepy
        sleep 1
    fi 2>&1 >> $logfile
done
