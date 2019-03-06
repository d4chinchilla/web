/* A log element of the UI                     *
 *  - elem is the DOM element of the log table */
function Log(elem)
{
    this.elem = elem;
    this.body = elem.getElementsByTagName("tbody")[0];
	
    /* Remove a row from the start of the log */
    this.pop_row = function()
    {
        this.body.removeChild(this.body.firstChild);
    }

    /* Add a row to the end of the log                   *
     *  - data is a list of strings to add, one for each *
     *    column.                                        */
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
