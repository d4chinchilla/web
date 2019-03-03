function Requestor()
{
    this.radar = new Radar(document.getElementById("ui-radar"));
    this.log   = new Log(document.getElementById("ui-log"));
    this.lastid = 0;

    this.add_row = function(data)
    {
        if (data.id <= this.lastid)
        {
            return;
        }

        this.lastid = data.id;
        this.push_row([data.angle, data.amplitude, data.speed, data.error])
    }

    this.add_blip = function(data)
    {
        this.radar.blip(data.angle, 0.5, data.amplitude);
    }

    this.on_sounds = function()
    {
        var text  = this.req.responseText;
        var lines = this.text.split("\n");

        for (line of lines)
        {
            var data = JSON.parse(line);
            this.add_row(data);
            this.add_blip(data);
        }
    }

    this.request_sounds = function()
    {
        this.req = new XMLHttpRequest();
        this.req.open("GET", "/sounds");
        this.req.onreadystatechange = this.on_sounds;
    }
}
