/* Add the styles to an element to make it a circular shape */
function circle_style(elem, radius)
{
    elem.style.borderRadius = radius.toString() + "px";
    elem.style.width        = (radius * 2).toString()+"px";
    elem.style.height       = (radius * 2).toString()+"px";
}

/* A radar element of the UI                              *
 *  - elem is the DOM node representing the radar element */
function Radar(elem)
{
    this.elem = elem;

    /* Initialize the radar element                   *
     *  - radius is the radius of the radar in pixels */
    this.init = function(radius)
    {
        this.radius = radius
        circle_style(elem, radius);

        for (var angle=0; angle < Math.PI - 0.01; angle += Math.PI / 6)
        {
            this.add_radial(angle);
        }

        for (var radius = 0.1; radius < 1; radius += 0.2)
        {
            this.add_circular(radius);
        }
    }

    /* Make a blip appear on the radar                                    *
     *  - angle is the angular position of the blip in radians            *
     *  - radius is the distance from the origin of the blip (0.0 to 1.0) *
     *  - size is the size of the blip (0.0 to 1.0)                       */
    this.blip = function(angle, radius, size)
    {
        var blip = new Blip(this);
        blip.init(angle, radius, size);
    }

    /* Add a radial line to the radar display *
     *  - the angle of the line (rads)        */
    this.add_radial = function(angle)
    {
        var elem = document.createElement("div");
        elem.className = "radial";

        elem.style.top   = this.radius.toString() + "px";
        elem.style.width = (2 * this.radius.toString()) + "px";
        elem.style.transform = "rotate(" + angle.toString() + "rad)";

        this.elem.appendChild(elem);
    }

    /* Add a circular line to the radar display  *
     *  - the radius to add the line to (0 to 1) */
    this.add_circular = function(radius)
    {
        var elem = document.createElement("div");
        elem.className = "circular";

        circle_style(elem, radius * this.radius);
        elem.style.top  = ((1 - radius) * this.radius).toString() + "px";
        elem.style.left = ((1 - radius) * this.radius).toString() + "px";

        this.elem.appendChild(elem);
    }
}

/* A blip on the radar                            *
 *  - radar is the Radar() where we want the blip */
function Blip(radar)
{
    this.radar = radar;

    /* Initialize the blip *
     *  - angle is the angular position of the blip in radians *
     *  - radius is the radial position of the blip (0 to 1)   *
     *  - size is the size of the blip (0 to 1)                */
    this.init = function(angle, radius, size)
    {
        this.elem = document.createElement("div");
        this.elem.className = "blip";
        this.radius = this.radar.radius * size / 25;

        circle_style(this.elem, this.radius);
        this.elem.style.left = this.get_xpos(angle, radius, size).toString() + "px";
        this.elem.style.top  = this.get_ypos(angle, radius, size).toString() + "px";

        this.radar.elem.appendChild(this.elem);
        var self = this;

        window.setTimeout(function () { self.fade() }, 1000);
        window.setTimeout(function () { self.kill() }, 4000);

        this.elem.style.display = "block";
    }

    /* Get the offset of the blip from its parent element in pixels */
    this.get_xpos = function(angle, radius, size)
    {
        var centre = Math.cos(angle) * radius * this.radar.radius;
        return (centre - this.radius) + this.radar.radius;
    }

    /* Get the offset of the blip from its parent element in pixels */
    this.get_ypos = function(angle, radius, size)
    {
        var centre = - Math.sin(angle) * radius * this.radar.radius;
        return (centre - this.radius) + this.radar.radius;
    }

    /* Cause this blip to start fading away to nothing */
    this.fade = function()
    {
        this.elem.style.transform = "scale(0)";
    }

    /* Cause this blip to stop existing */
    this.kill = function()
    {
        this.radar.elem.removeChild(this.elem);
    }
}


