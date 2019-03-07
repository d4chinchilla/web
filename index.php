<!--Co-written by Matt Crossley and Francis Wharf-->

<!--This file is the main web-page providing the interface
as well as providing controls for uploading new firmware and calibrating the device-->
<!--The document combines PHP and Javascript into an HTML script-->
<!DOCTYPE html>
<html>
    <head>
        <title>D4 UI</title>
<!--    Reference .scc style sheet-->
        <link rel="stylesheet" href="style.css">

<!-- Load in radar and log javascript for radar display elements-->
        <script src="radar.js"></script>
        <script src="log.js"></script>
        <script src="requestor.js"></script>

<!--    Load in JQuery and Canvas script for FFT display-->
        <script type="text/javascript" src="jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="jquery.canvasjs.min.js"></script>
        <script type="text/javascript" src="canvasjs.min.js"></script>
        <script type="text/javascript">
//          All below code is ran on load of the web page.
            window.onload = function()
            {
                //Empty array to be filled from JSON
                var FFTdata = [];
              /* Create new chart container for FFT data
              * Utilising Canvas.js API for good looking dynamic data*/
                var chart = new CanvasJS.Chart("chartContainer", {
                    //Allow user to mouse over data to see exact data point values
                    interactivityEnabled: true,
                    title: {
                        text: "Amplitude Response"
                    },
                    // Set X axis as logarithmic
                    axisX: {
                        logarithmic: true,
                        title: "Frequency (Hz)",
                        minimum: 50,
                        maximum: 2000
                    },
                    axisY: {
                        title: "Magnitude (dB)",
                        minimum: 0,
                        maximum: 15000
                    },
                    //Specify line type chart using data array
                    data: [
                        {
                            type: "line",
                            dataPoints: FFTdata
                        }
                    ]
                });
            //chart.render();
            /*to pull data from dynamic .JSON, function is run from a JQuery request that
            * sends the function the JSON in its entirety */
            function pollFFT(data) {
                console.log("Made it here");
                //Pull all key-value pairs from fft key and store in freq
                var freq = data.fft;
                console.log(freq);
                var i = 1;
                //Loop through each key value pair and assign values to the FFTdata array in 50Hz increments
                for(var key in freq) {
                    var specFreq = freq[key];
                    FFTdata[i-1] = {
                        x: (i * 50),
                        y: specFreq
                    };
                    i++;
                }
                console.log(FFTdata);
                //At end of loop, render chart on webpage
                chart.render();
            }

            //Function to pull JSON using JQuery and parse data to poll function
            var updateChart = function() {
                $.getJSON("fft.json",pollFFT);
            };

            //Poll JSON data and update chart every 50ms
            var updateInterval = 50;
            updateChart();
            setInterval(function(){updateChart()},updateInterval);

            //Initialise radar display
            var req_radar = new Requestor();
            req_radar.radar.init(200);

            //Add a new radar blip every second
            window.setInterval(function()
            {
                Requestor.request_sounds();
            }, 50);




            }
        </script>
    </head>
    <body>
<!--    Set PHP settings so that any errors are displayed on the page-->
        <?php ini_set('display_errors', 'On'); error_reporting(E_ALL | E_STRICT | E_NOTICE); ?>
	    <!-- <?php phpinfo();?> -->
        <?php
//          Conditional statement for when restart/calibration buttons are pressed
        // If words "restart" or "calibrate" are posted by buttons, these conditionals will run the required functions
            if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['restart']))
            {
                restartPi();
            } else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['calibrate']))
            {
                calibratePi();
            }
            //Restart function writes "reset" to control file to initiate pi reboot
            function restartPi()
            {
                $filePath = fopen("chinchilla-reset", "w");
                //echo $filePath;
                //if(!$filePath) {echo "File Open failed";}
                //echo "Writing";
                fwrite($filePath, "reset\n");
                //echo "Closing";
                fclose($filePath);
            }
            //Calibrate function writes "calibrate" to control file to initiate system calibration
            function calibratePi() {
                $filePath = fopen("chinchilla-reset", "w");
                fwrite($filePath,"calibrate\n");
                fclose($filePath);
            }
        ?>
<!--This form is the front end display for the file upload, the file handling is all performed by upload.php-->
	<form action="upload.php" method="post" enctype="multipart/form-data">
    		Select firmware to upload:
    		<input type="file" name="fileToUpload" id="fileToUpload">
    		<input type="submit" value="Upload Firmware" name="submit">
	</form>
<!--This form provides the restart and calibrate buttons on the main page-->
    <form action="index.php" method="post">
        <input type="submit" name="restart" value="Restart Pi" />
        <input type="submit" name="calibrate" value="Calibrate Device" />
    </form>

<!--Instantiate the chart, radar and log displays in the main body-->
    <div id="chartContainer" style="height: 200px; width: 100%;"></div>
	<div id="ui">
            <div id="ui-radar" class="radar">
            </div>
            <div id="ui-log" class="log">
                <table id="ui-log-table" class="log-table">
                    <thead>
                        <tr>
                            <th>Angle</th>
                            <th>Amplitude</th>
                            <th>Speed</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
