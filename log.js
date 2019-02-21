function Log(elem)
{
    this.elem = elem;
    this.body = elem.getElementsByTagName("tbody")[0];
	
    this.pop_row = function()
    {
        this.body.removeChild(this.body.firstChild);
    }
    
    this.push_row = function(data)
    {
        var row = document.createElement("tr");
        for (str of data)
        {
            var textnode = document.createTextNode(str);
            var cell     = document.createElement("td");
            cell.appendChild(textnode);
            row.appendChild(cell);
        }
        this.body.appendChild(row);
    }
}