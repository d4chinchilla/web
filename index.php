<!DOCTYPE html>
<html>
    <head>
        <title>D4 UI</title>
        <link rel="stylesheet" href="style.css">
        <script src="radar.js"></script>
        <script src="log.js"></script>
        <script type="text/javascript" src="canvasjs.min.js"></script>
        <script type="text/javascript">
            window.onload = function()
            {
                var chart = new CanvasJS.Chart("chartContainer", {
                    zoomEnabled: true,
                    zoomType: "xy",
                    interactivityEnabled: false,
                    title: {
                        text: "Amplitude Response"
                    },
                    axisX {
                        logarithmic: true,
                        title: "Frequency (Hz)",
                        minimum: 1
                    },
                    axisY: {
                        title: "Magnitude (dB)",
                    }
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
                chart.render();
                var radar = new Radar(document.getElementById("ui-radar"));
                radar.init(200);

                window.setInterval(function()
                {
                    radar.blip(Math.random() * 2 * Math.PI, Math.random(), Math.random());
                }, 1000);
            }
        </script>
    </head>
    <body>
        <?php ini_set('display_errors', 'On'); error_reporting(E_ALL | E_STRICT); ?>
	<!-- <?php phpinfo();?> -->
	<form action="upload.php" method="post" enctype="multipart/form-data">
    		Select firmware to upload:
    		<input type="file" name="fileToUpload" id="fileToUpload">
    		<input type="submit" value="Upload Firmware" name="submit">
	</form>
    <div id="chartContainer" style="height: 500px; width: 50%;"></div>
	<div id="ui">
            <div id="ui-radar" class="radar">
            </div>
            <div id="ui-log" class="log">
                <table id="ui-log-table" class="log-table">
                    <thead>
                        <tr>
                            <th>Angle</th>
                            <th>Amplitude</th>
                            <th>&Delta;t1</th>
                            <th>&Delta;t2</th>
                            <th>&Delta;t3</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
