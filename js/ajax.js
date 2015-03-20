/**
 * General AJAX Functions
 * 
 * These are a few AJAX related functions that can be used for general purpose.
 * 
 * @author  James M Irvine
 * @since   1.0.0
 */



/**
 * Name: Create Request
 * Description:
 *   Creates a browser-dependent AJAX Request Object.
 * Params: void
 * Return:
 *   AJAX Request Object
 */
function createRequest()
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
 * Name: Get HTML
 * Description:
 *   Sends a request to the given URL and prints the response to the given HTML
 *   Element. 
 * Params:
 *   url - The URL to send the request
 *   onComplete - A function with one string argument
 *   post - If empty, sends GET request, otherwise sends this argument via post
 * Return: void
 */
function getHTML(url,onComplete,post)
{
    if (url==null || onComplete==null) return;
	post = typeof post !== 'undefined' ? post : "";
    
    var theRequest = createRequest();
    
	if (post=="")
	{
    	theRequest.open("GET", url, true);
	}
	else
	{
    	theRequest.open("POST", url, true);
	}
    theRequest.onreadystatechange = function()
    {
        if (theRequest.readyState == 4)
        {
            if (theRequest.status == 200)
            {
                onComplete(theRequest.responseText);
            }
            else 
            {
                alert("AJAX Error " + theRequest.status);
            }
        }
    };
	if (post=="")
	{
	    theRequest.send(null);
	}
	else
	{
		theRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		theRequest.send(post);
	}
}