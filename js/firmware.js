/**
 * Firmware Scripts
 * 
 * This file contains the scripts used on the Firmware page.
 * 
 * @author  James M Irvine
 * @since   1.0.0
 */



// The global FirmwareInfo Object
var theTabs = null;
// The Updater Object
var theUpdater;

/**
 * Name: Validate Form
 * Description:
 *   Performs check on form submission for required entries.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
function validateForm(aform)
{
    if (aform.firmware.value=="")
    {
        alert("Select firmware type!");
        return false;
    }
    if (aform.version.value=="")
    {
        alert("Select version type!");
        return false;
    }
    return true;
}

/**
 * Name: Version Update
 * Description:
 *   Retrieves and displays the version list based on form information.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
function versionUpdate(aform)
{
    hideUpdaterBtn();
    if (aform.firmware.value=="")
    {
        document.getElementById("versions").innerHTML = "";
        return false;
    }
    
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "get_versions.php?c=" + encodeURI(aform.component.value) + "&f=" + encodeURI(aform.firmware.value);
    
    getHTML(url,function(resp)
    {
        document.getElementById("versions").innerHTML = resp;
    });
}

/**
 * Name: Set Updater
 * Description:
 *   Sends request to the server to set the new updater firmware based on the
 *   given form information.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
function setUpdater(aform)
{
    if (aform.firmware.value=="")
    {
        alert("Select firmware type!");
        return false;
    }
    var cver = "";
    for (var i = 0, length = aform.curversion.length; i < length; i++)
    {
        if (aform.curversion[i].checked)
        {
            cver = aform.curversion[i].value;
            break;
        }
    }
    
    var url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "set_updater.php?c=" + encodeURI(aform.component.value) + "&f=" + encodeURI(aform.firmware.value) + "&v=" + encodeURI(cver);
    
    getHTML(url,function(resp)
    {
        if (resp=="true")
            versionUpdate(aform);
    });
}

/**
 * Name: Show Updater Button
 * Description:
 *   Used only in onclick events for version selection. Displays the set button.
 * Params:
 *   obj - HTML Element that was clicked
 * Return: void
 */
function showUpdaterBtn(obj)
{
    document.getElementById("updaterbtn").style.display = "block";
    obj.getElementsByTagName("input")[0].checked = "true";
}

/**
 * Name: Hide Updater Button
 * Description:
 *   Hides the set button.
 * Params: void
 * Return: void
 */
function hideUpdaterBtn()
{
    document.getElementById("updaterbtn").style.display = "";
}

/**
 * Name: Toggle Component Specific Displays
 * Description:
 *   Hides/Shows buttons/inputs for the current component.
 * Params: void
 * Return: void
 */
function toggleComponentSpecific(component)
{
    switch(component)
    {
    case "Chassis":
        document.getElementById("cartridge-show").style.display = "none";
        document.getElementById("switch-show").style.display = "none";
        document.getElementById("chassis-show").style.display = "";
        document.getElementById("chassis-hide").style.display = "none";
        document.getElementById("all-show").value = "Update All Chassis Firmware";
        break;
    case "Cartridge":
        document.getElementById("cartridge-show").style.display = "";
        document.getElementById("switch-show").style.display = "none";
        document.getElementById("chassis-show").style.display = "none";
        document.getElementById("chassis-hide").style.display = "";
        document.getElementById("all-show").value = "Update All Cartridge Firmware";
        break;
    case "Switch":
        document.getElementById("cartridge-show").style.display = "none";
        document.getElementById("switch-show").style.display = "";
        document.getElementById("chassis-show").style.display = "none";
        document.getElementById("chassis-hide").style.display = "";
        document.getElementById("all-show").value = "Update All Switch Firmware";
        break;
    }
    document.firmware.which.value = "";
}

function updateSelected()
{
    if (document.firmware.chassis.value == "")
    {
        alert("Select a chassis!");
        return;
    }
    if (document.firmware.firmware.value == "")
    {
        alert("Select a firmware type!");
        return;
    }
    
    showOverlay(2);
    theUpdater.validated = false;
    theUpdater.query = "";
    theUpdater.update(
        document.firmware.component.value, 
        document.firmware.firmware.value, 
        document.firmware.chassis.value, 
        document.firmware.which.value);
}

function updateAll()
{
    if (document.firmware.chassis.value == "")
    {
        alert("Select a chassis!");
        return;
    }

    showOverlay(2);
    theUpdater.validated = false;
    theUpdater.query = "";
    theUpdater.update(
        document.firmware.component.value,
        '',
        document.firmware.chassis.value,
        document.firmware.which.value);
}

function updateChassis()
{
    if (document.firmware.chassis.value == "")
    {
        alert("Select a chassis!");
        return;
    }

    showOverlay(2);
    theUpdater.validated = false;
    theUpdater.query = "";
    theUpdater.update(
        '',
        '',
        document.firmware.chassis.value,
        '');
}

function refreshFirmware()
{
    getHTML("refresh_firmware.php",function() {window.location.href += "";});
}

/**
 * Name: Initialize Firmware Page
 * Description:
 *   Creates the FirmwareInfo object, and sets the first tab.
 * Params: void
 * Return: void
 */
function init()
{
    theTabs = new FirmwareInfo(document.firmware);
    getElementsByClassName(getElementsByClassName(document.body,"tabbed")[0],"start")[0].getElementsByTagName("a")[0].click();
    theUpdater = new Updater(document.getElementById("overlay-container-2").getElementsByTagName("p")[0],document.getElementById("show_update"));
}

/**
 * Name: Firmware Info Class Constructor
 * Description:
 *   Creates an FirmwareInfo object for use with the specified HTML Form.
 *   Requires functions from Ajax.js and General.js.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
function FirmwareInfo(theForm)
{
    this.form           = theForm;      // HTML Form used for Firmwareing
    this.tabs           = {};           // Holds all the tab info
    this.curTab         = null;         // The current tab
    this.firstLoad      = true;         // Flag for first tab load
}

/**
 * Name: Firmware Info::Add Tab
 * Description:
 *   Gets info for a new tab based on the given component.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
FirmwareInfo.prototype.addTab = function(component)
{
    this.tabs[component] = {};

    var _that = this;    
    url = location.href.substring(0,location.href.lastIndexOf("/")+1) + "get_firmware_list.php?c=" + encodeURI(component);
    getHTML(url,function(resp)
    {
        _that.tabs[component]['firmware_list'] = resp;
        if (!_that.firstLoad)
        {
            _that.form.firmware.parentElement.innerHTML = resp;
        }
        else
        {
            _that.firstLoad = false;
        }
    });
}

/**
 * Name: Firmware Info::Swap Tabs
 * Description:
 *   Handles the information swap when changing tabs.
 * Params:
 *   theForm - HTML Form to work with
 * Return: void
 */
FirmwareInfo.prototype.swapTabs = function(obj,component)
{
    if (this.curTab!=null)
    {
        removeClass(this.curTab,"here");
    }
    else 
    {
        removeClass(obj.parentElement,"start");
    }
    this.curTab = obj.parentElement;
    if (!this.firstLoad)
        this.form.firmware.parentElement.innerHTML = '<select name="firmware" onchange="versionUpdate(this.form.curversion);"><option selected="selected" value="">Loading firmware list...</option></select>';
    this.form.component.value = component;
    if (this.tabs[component]==undefined)
    {
        this.addTab(component);
    }
    else
    {
        this.form.firmware.parentElement.innerHTML = this.tabs[component]['firmware_list'];
    }
    this.curTab.className += " here";
    versionUpdate(theTabs.form);
    toggleComponentSpecific(component)
}
