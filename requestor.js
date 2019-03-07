/* A class to handle updating the radar and log */
function Requestor()
{
    /* Get the elements in the document corresponding to the radar and log */
    this.radar = new Radar(document.getElementById("ui-radar"));
    this.log   = new Log(document.getElementById("ui-log"));
    this.lastid = 0;

    /* Add a row of data to the log ui element.                     *
     *  - data contains information about a sound, in the format of *
     *    /tmp/chinchilla-sounds.                                   */
    this.add_row = function(data)
    {
        /* Reject data with an id lower than one we've already seen */
        if (data.id <= this.lastid)
        {
            return;
        }

        /* Increment our last-seen id and add the data to the log */
        this.lastid = data.id;
        this.push_row([data.angle, data.amplitude, data.speed, data.error])
    }


    /* Add a row of data to the radar display.                      *
     *  - data contains information about a sound, in the format of *
     *    /tmp/chinchilla-sounds.                                   */
    this.add_blip = function(data)
    {
        /* Reject data with an id lower than one we've already seen */
        if (data.id <= this.lastid)
        {
            return;
        }
     
        /* Add the blip to the UI element */
        this.radar.blip(data.angle, 0.5, data.amplitude);
    }

    /* A callback function called when data is ready from a request. */
    this.on_sounds = function()
    {
        /* Get the text of the file */
        var text  = this.req.responseText;
        /* Split up the lines */
        var lines = text.split("\n");

        /* Each line contains JSON, so we iterate over each one */
        for (line of lines)
        {
            /* Individually parse each line */
            var data = JSON.parse(line);
            /* Process the data */
            this.add_row(data);
            this.add_blip(data);
        }
    }

    /* Make a request to the server to get information about current sounds */
    this.request_sounds = function()
    {
        /* Create and store the reqtuest */
        this.req = new XMLHttpRequest();
        this.req.open("GET", "/sounds");
        /* Mound the callback */
        this.req.onreadystatechange = this.on_sounds;
    }
}
