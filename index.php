<!DOCTYPE html>
<html>
    <head>
        <title>D4 UI</title>
        <link rel="stylesheet" href="style.css">


        <script src="radar.js"></script>
        <script src="log.js"></script>

        <script type="text/javascript" src="jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="jquery.canvasjs.min.js"></script>
        <script type="text/javascript" src="canvasjs.min.js"></script>
        <script type="text/javascript">
            window.onload = function()
            {

                var FFTdata = [];

                var chart = new CanvasJS.Chart("chartContainer", {
                    interactivityEnabled: true,
                    title: {
                        text: "Amplitude Response"
                    },
                    axisX: {
                        logarithmic: true,
                        title: "Frequency (Hz)",
                        minimum: 1,
                        maximum: 10000
                    },
                    axisY: {
                        title: "Magnitude (dB)"
                    },
                    data: [
                        {
                            type: "line",
                            dataPoints: [
                                {x: 1, y: 10},
                                {x: 10, y: 1}
                            ]
                        }
                    ]
                });
            //chart.render();

            function pollFFT(data) {
                console.log("Made it here");
                var freq = data.fft;
                console.log(data.fft);
                for(var i = 0; i < freq.length; i++) {
                    FFTdata[i] = {
                        x: freq[i][0],
                        y: freq[i][1]
                    }
                }
                chart.render();
            }


            var radar = new Radar(document.getElementById("ui-radar"));
            radar.init(200);

            window.setInterval(function()
            {
                radar.blip(Math.random() * 2 * Math.PI, Math.random(), Math.random());
            }, 1000);


            $.getJSON("fft.json", pollFFT);

            }
        </script>
    </head>
    <body>

        <?php ini_set('display_errors', 'On'); error_reporting(E_ALL | E_STRICT | E_NOTICE); ?>
	    <!-- <?php phpinfo();?> -->
        <?php
            $dataPath = '/var/www/chinchilla-fft';
            $dataJSON = file_get_contents($dataPath);
            $dataArray = json_decode($dataJSON,true);
            //print_r($dataArray);
            echo $dataArray[0]["fft"];
        ?>
        <?php
            if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['restart']))
            {
                restartPi();
            } else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['calibrate']))
            {
                calibratePi();
            }
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
            function calibratePi() {
                $filePath = fopen("chinchilla-reset", "w");
                fwrite($filePath,"calibrate\n");
                fclose($filePath);
            }
        ?>
	<form action="upload.php" method="post" enctype="multipart/form-data">
    		Select firmware to upload:
    		<input type="file" name="fileToUpload" id="fileToUpload">
    		<input type="submit" value="Upload Firmware" name="submit">
	</form>
    <form action="index.php" method="post">
        <input type="submit" name="restart" value="Restart Pi" />
        <input type="submit" name="calibrate" value="Calibrate Device" />
    </form>
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
