#!/bin/bash

fname="/tmp/chinchilla-reset"

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
