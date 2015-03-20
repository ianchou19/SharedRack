/**
 * Updater Class
 * 
 * This class is designed to handle component update requests using AJAX
 * requests. Updater requests command 
 * 
 * @author  James M Irvine
 * @since   1.0.0
 */



/**
 * Name: Updater Class Constructor
 * Description:
 *   Creates an Updater object for use with the specified HTML Elements.
 * Params:
 *   logElem - HTML Element holding the log
 * Return: void
 */
function Updater(logElem,showBtn)
{
    this.primRequest    = null;     // Holds the primary AJAX Request Object
    this.logRequest     = null;     // Holds the log AJAX Request Object
    this.timer          = null;     // Holds timer object
    this.logElem        = logElem;  // HTML Element holding the log
    this.showBtn        = showBtn;  // HTML Element to show the log
    this.follow         = true;     // Flag variable for scrolling with the log
    this.query          = "";       // Query string for POST
    this.validated      = false;    // Flag indicating if a validation has been done
    this.chassis        = "";       // The IP of the chassis being updated
    this.stopped        = false;    // If there was a kill request
}

/**
 * Name: Update
 * Description:
 *   Performs initial request to start the update, and starts logging update output.
 * Params: 
 *   [l0] - Chassis IP
 *   [l1] - Cartridge or Switch
 *   [l2] - Node
 * Return: void
 */
Updater.prototype.update = function()
{
    if ((this.primRequest = this.createRequest())==undefined) return;
    this.logElem.innerHTML = "";
    var theUpdater = this;
    if (this.query=="")
    {
        this.query = "ctype=" + arguments[0] +
                    "&ftype=" + arguments[1] +
                    "&ip=" + arguments[2] +
                    "&component=" + arguments[3];
        this.chassis = arguments[2];
        this.logElem.innerHTML += "> Refreshing chassis... ";
    }
    else
    {
        this.query += "&flag=true";
    }
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "update.php";
    this.primRequest.open("POST", url, true);
    this.primRequest.onreadystatechange = function() {
        if (this.readyState == 4)
        {
            if (this.status == 200)
            {
                if (this.responseText=="")
                {
                    theUpdater.logRequest = theUpdater.createRequest();
                    theUpdater.getLog();
                }
                else
                {
                    hideOverlay(2);
                    alert("Update Script Error: " + this.responseText);
                }
            }
            else
            {
                alert("Update AJAX Error " + this.status);
            }
        }
    };
    this.primRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    this.primRequest.send(this.query);
}

/**
 * Name: Create Request
 * Description:
 *   Creates a browser-dependent AJAX Request Object.
 * Params: void
 * Return:
 *   AJAX Request Object
 */
Updater.prototype.createRequest = function()
{
    var request = null;
    try {
        request = new XMLHttpRequest();
    } catch (trymicrosoft) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (othermicrosoft) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed) {
                request = null;
            }
        }
    }
 
    if (request == null) {
        alert("Error creating request object!");
    } else {
        return request;
    }
}

/**
 * Name: Kill Update
 * Description:
 *   Requests update process be killed
 * Params: void
 * Return: void
 */
Updater.prototype.kill = function()
{
    var _that = this;
    clearTimeout(this.timer);
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "stop_update.php";
    
    this.stopped = true;
    this.logRequest.open("GET", url, true);
    this.logRequest.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            _that.getLog();
        }
    };
    this.logRequest.send(null);
}

/**
 * Name: Get Log
 * Description:
 *   Prints all the output of current update operation 
 * Params: void
 * Return: void
 */
Updater.prototype.getLog = function()
{
    if (this.logElem==null) return;
    if (this.logRequest==null)
    {
        if ((this.logRequest = this.createRequest())==undefined) return;
    }
    
    this.showBtn.style.display = "";
    
    var _that = this;
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "get_update_log.php";
    
    this.logRequest.open("GET", url, true);
    this.logRequest.onreadystatechange = this.showLog(this);
    this.logRequest.send(null);
    
    this.timer = setTimeout(function() {_that.getLog()},5000);
}

/**
 * Name: Show Log
 * Description:
 *   Prints the requested log to the logElem and scrolls if at bottom.
 * Params:
 *   theUpdater - The associated Updater object.
 * Return: void
 */
Updater.prototype.showLog = function(theUpdater)
{
    return function() {
        if (this.readyState == 4)
        {
            if (this.status == 200 && this.responseText.length>0)
            {
                if (this.responseText=="[VALIDATION_UPDATE_STEP=TRUE]")
                {
                    theUpdater.stopLog();
                    if (!theUpdater.validated && !theUpdater.stopped)
                    {
                        theUpdater.logElem.innerHTML += "> Refreshing chassis... ";
                        if (theUpdater.follow)
                        {
                            theUpdater.logElem.scrollTop = theUpdater.logElem.scrollHeight - theUpdater.logElem.clientHeight;
                        }
                        getHTML("../../refresh.php?c=" + theUpdater.chassis,function()
                        {
                            theUpdater.getUnvalidatedUpdates();
                            theUpdater.validated = true;
                        });
                    }
                    else
                    {
                        theUpdater.logElem.innerHTML += "> Execution complete.";
                        if (theUpdater.follow)
                        {
                            theUpdater.logElem.scrollTop = theUpdater.logElem.scrollHeight - theUpdater.logElem.clientHeight;
                        }
                        theUpdater.stopped = false;
                    }
                }
                else
                {
                    theUpdater.logElem.innerHTML = this.responseText;
                    if (theUpdater.follow)
                    {
                        theUpdater.logElem.scrollTop = theUpdater.logElem.scrollHeight - theUpdater.logElem.clientHeight;
                    }
                }
            }
            else 
            {
                if (this.status != 200)
                    alert("Log AJAX Error " + this.status);
                theUpdater.stopLog();
                this.query = "";
            }
        }
    }
}

Updater.prototype.getUnvalidatedUpdates = function()
{
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "get_unvalidated.php";
    
    theUpdater.logRequest.open("GET", url, true);
    theUpdater.logRequest.onreadystatechange = theUpdater.catchUnvalidatedUpdates(theUpdater);
    theUpdater.logRequest.send(null);
}

Updater.prototype.catchUnvalidatedUpdates = function(theUpdater)
{
    return function() {
        if (this.readyState == 4)
        {
            if (this.status == 200 && this.responseText.length>0)
            {
                theUpdater.validateUpdates(this.responseXML);
            }
            else 
            {
                if (this.status != 200)
                {
                    alert("Log AJAX Error " + this.status);
                    return;
                }
                theUpdater.update();
            }
        }
    }
}

Updater.prototype.validateUpdates = function(updatesXML)
{
    var firmware = updatesXML.getElementsByTagName("firmware");
    for(var i=0; i<firmware.length; i++)
    {
        var file = firmware[i].firstChild.data;
        file = file.substr(file.lastIndexOf("/") + 1);
        var str = "For the following:" + 
            "\nFirmware Type:    " + firmware[i].attributes.type.value +
            "\nVersion:    " + firmware[i].attributes.version.value + 
            "\nFile:    " + file +
            "\n\nChange the version to the following value found after update?\n" +
            firmware[i].attributes.curVersion.value;
        if (confirm(str))
        {
            var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "set_validated.php?c=" + firmware[i].attributes.component.value + "&f=" + firmware[i].attributes.type.value + "&v=" + firmware[i].attributes.curVersion.value;

            this.logRequest.open("GET", url, true);
            this.logRequest.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status != 200)
                {
                    alert("Log AJAX Error " + this.status);
                }
            };
            this.logRequest.send(null);
        }
    }
    this.update();
}

/**
 * Name: Toggle Follow
 * Description:
 *   Toggles the scroll following effect.
 * Params: void
 * Return: void
 */
Updater.prototype.toggleFollow = function(obj)
{
    if (this.follow)
    {
        this.follow = false;
        obj.innerHTML = "Follow Off";
    }
    else
    {
        this.follow = true;
        obj.innerHTML = "Follow On";
    }
}

/**
 * Name: Stop Log
 * Description:
 *   Stops the log requests.
 * Params: void
 * Return: void
 */
Updater.prototype.stopLog = function()
{
    this.showBtn.style.display = "none";
    clearTimeout(this.timer);
    if (this.follow)
    {
        this.logElem.scrollTop = this.logElem.scrollHeight - this.logElem.clientHeight;
    }
}

