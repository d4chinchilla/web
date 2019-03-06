#!/bin/bash

#Co-written by Francis Wharf and Matt Crossley

#This script is run as sudo at boot and is controlled by a pipe that the web-server can write to.
#As the script has sudo it can perform commands that are required to install and run the various software/firmware.
#This script handles all installation of software/firmware as well as interpreting uploaded files and ensuring they have correct signature

#Specify the locations of control and log files
fname="/var/www/html/chinchilla-reset"
logfile="/tmp/chinchilla-log"
ctlfile="/tmp/chinchilla-backend-ctl"

#If pipe does not exist, make pipe
[[ -p $fname ]] || mkfifo $fname
#If log exists, remove log file
[[ -f $logfile ]] && rm $logfile


#Move temp files to web-server root so server can utilise the files (permission problems otherwise)
hack_file()
{
    while true; do
        if [[ -f /tmp/chinchilla-fft ]]; then
            cp /tmp/chinchilla-fft /var/www/html/fft.json
            cp /tmp/chinchilla-sounds /var/www/html/sounds
        fi
        #Move every 50ms
        sleep 0.05
    done
}
#Run in background on boot
hack_file &

shutdown()
{
    #Shutdown services function, if run this will terminate the back-end processes by writing to its control file
    echo SHUTTING DOWN!
    [[ -p $ctlfile ]] && echo stop > $ctlfile
}

start()
{
    #Start services function, run at boot to start all software
    echo STARTING!
    #Extra sleep time to ensure all block devices and external controllers are recognised and set up
    sleep 10
    # Serial channel setup
    stty -F /dev/ttyACM0 406:0:18b4:8a30:3:1c:7f:15:4:2:64:0:11:13:1a:0:12:f:17:16:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0
    #Run software start scripts
    /var/www/backend &
    python3 /var/www/led_ctl.py &
}

install()
{
    echo INSTALLING!
    #Flash terminal to show script has run
    lxterminal -e echo Hello!
    #If zip already exists, delete to prevent gpg from asking to overwrite
    rm /var/www/html/uploads/firmware.zip
    echo $?
    #Extract .zip from signed file if file correctly signed
    gpg -o /var/www/html/uploads/firmware.zip -d /var/www/html/uploads/firmware.zip.gpg
    result=$?
    #Log result (0 is correctly signed, 2 is incorrectly signed)
    echo $result
    #If correctly signed, proceed
    if [[ $result -eq "0" ]]; then
	    echo Unzipping!
	    #Remove all files from extract folder
	    rm /var/www/html/fwExtract/*
	    #Unzip .zip to extract folder - log all files extracted
        unzip /var/www/html/uploads/firmware.zip -d /var/www/html/fwExtract 2>file
        echo Extracting!
        #Set install directory for microcontroller firmware
        instdir=$(echo /media/pi/NODE_L432KC* | cut -d " " -f 1)
        echo "$instdir"
        #If microcontroller exists, copy firmware binary into controller folder.
        [[ -d "$instdir" ]] && cp /var/www/html/fwExtract/*.bin "$instdir"
##        cp /var/www/html/fwExtract/*.bin /media/pi/NODE_L432KC
#       find /var/www/html/fwExtract -iname '*.bin' -exec cp {} /media/pi/NODE_L432K* \;
    else
        echo Incorrectly signed file!
    fi
}

reset()
{
    #Restart raspberry pi
    echo RESTARTING PI!
    reboot
}

calibrate()
{
    #Tell back-end to begin a calibration (write to control file)
    echo Initialising calibration!
    [[ -p $ctlfile ]] && echo calibrate > $ctlfile
}

#Install to microcontroller and start processes on boot, plenty of sleep time to make sure external hardware is recognised
sleep 5
install
sleep 10
start
#Loop constantly looking at pipe
while true; do
    #Read controlling file and store line on pipe
    if read -r line < $fname; then
        echo $line
        #Use case to determine next set of actions, depending on word, different functions are run
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
        calibrate)
            calibrate
            ;;
        esac
    else
        #Sleep for a second in every loop to reduce CPU load
        echo Sleepy
        sleep 1
    fi 2>&1 >> $logfile
    #Log all echos
done

