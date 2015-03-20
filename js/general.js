/**
 * General Functions
 * 
 * This file contains general purpose functions useful in other files. 
 * 
 * @author	James M Irvine
 * @since	1.0.0
 */


// Global flag for MS Internet Explorer
var isMSIE = false;
/*@cc_on 
isMSIE = true;
@*/

/**
 * Name: Show Overlay
 * Description:
 *   Displays the log overlay.
 * Params: void
 * Return: void
 */
function showOverlay(num)
{
    document.getElementById("overlay").style.display = "block";
    document.getElementById("overlay-" + num).style.display = "block";
}

/**
 * Name: Hide Overlay
 * Description:
 *   Hides the log overlay.
 * Params: void
 * Return: void
 */
function hideOverlay(num)
{
    document.getElementById("overlay").style.display = "none";
    document.getElementById("overlay-" + num).style.display = "none";
}

/**
 * Name: Set Opacity
 * Description:
 *   Changes the opacity, or transparency of an element.
 * Params:
 *   obj - HTML Element to change
 *   value - The percent opacity (0-10).
 * Return: void
 */
function setOpacity(obj,value) {
	obj.style.opacity = value/10;
	obj.style.filter = 'alpha(opacity=' + value*10 + ')';
}

/**
 * Name: Text Blink
 * Description:
 *   Blinks the text color between two colors and leaves it on the second.
 * Params:
 *   obj - HTML Element to change
 *   color1 - The first color
 *   color2 - The second color
 * Return: void
 */
function textBlink(obj,color1,color2) {
    setTimeout(function()
    {
        obj.style.color = color1;
    }, 200);
    setTimeout(function()
    {
        obj.style.color = color2;
    }, 400);
    setTimeout(function()
    {
        obj.style.color = color1;
    }, 600);
    setTimeout(function()
    {
        obj.style.color = color2;
    }, 800);
}

/**
 * Name: Get Elements By Class Name
 * Description:
 *   Retrieves an array of all the HTML Elements which hold the given class name.
 * Params:
 *   classname - The classname to be searched
 * Return:
 *   HTML Element Array
 */
function getElementsByClassName(node,classname)
{
    var a = [];
    var re = new RegExp('(^| )'+classname+'( |$)');
    var els = node.getElementsByTagName("*");
    for(var i=0,j=els.length; i<j; i++)
        if(re.test(els[i].className))a.push(els[i]);
    return a;
}

/**
 * Name: String::Trim
 * Description:
 *   Inclusion of a string trimming function for browsers who lack support
 *   for it. Removes leading and trailing whitespace from a string.
 * Params:
 *   remove - The classname to be removed.
 * Return: void
 */
if (!String.prototype.trim)
{
    String.prototype.trim = function()
    {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

/**
 * Name: Remove Class
 * Description:
 *   Removes the given class from the element's class list.
 * Params:
 *   elem - The element to remove from.
 *   remove - The classname to be removed.
 * Return: void
 */
function removeClass(elem,remove)
{
    var newClassName = "";
    var i;
    var classes = elem.className.split(" ");
    for(i = 0; i < classes.length; i++)
    {
        if(classes[i] !== remove)
        {
            newClassName += classes[i].trim();
            if(i<classes.length-1 && classes[i]!="") newClassName += " ";
        }
    }
    elem.className = newClassName;
}

