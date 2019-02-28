#!/bin/bash

fname="/var/www/html/chinchilla-reset"

[[ -p $fname ]] || mkfifo $fname

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
    cp /var/www/html/style.css /home/pi/D4
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
    fi
done
