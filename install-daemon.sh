#!/bin/bash

fname="/var/www/html/chinchilla-reset"
logfile="/tmp/chinchilla-log"
ctlfile="/tmp/chinchilla-backend-ctl"

[[ -p $fname ]] || mkfifo $fname
[[ -f $logfile ]] && rm $logfile

shutdown()
{
    echo SHUTTING DOWN!
    [[ -p $ctlfile ]] || echo stop > $ctlfile
    # Here, put code to stop all current processes
}

start()
{
    echo STARTING!

    # MUY IMPORTANTE! This is a hack and must die soon. Goodnight.
    cp /home/pi/D4/signal_processing/ARM/final_binary.bin /media/pi/NODE_L432KC
    sleep 10
    stty -F /dev/ttyACM0 406:0:18b4:8a30:3:1c:7f:15:4:2:64:0:11:13:1a:0:12:f:17:16:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0
    /var/www/backend &
    # Here, put code to start a new set of processes
}

install()
{
    echo INSTALLING!
    # Here, put code to verify an install file and install it
    lxterminal -e echo Hello!
    rm /var/www/html/uploads/firmware.zip
    echo $?
    gpg -o /var/www/html/uploads/firmware.zip -d /var/www/html/uploads/firmware.zip.gpg
    result=$?
    echo $result
    if [[ $result -eq "0" ]]; then
	    echo Unzipping!
	rm /var/www/html/fwExtract/*
        unzip /var/www/html/uploads/firmware.zip -d /var/www/html/fwExtract 2>file
        echo Extracting!
        find /var/www/html/fwExtract -iname '*.bin' -exec cp {} /media/pi/NODE_L432K* \;
    else
        echo Incorrectly signed file!
    fi
}

reset()
{
    echo RESTARTING PI!
    reboot
}

sleep 5
install
sleep 10
start
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
