/**
 * Similar xmlhttp request solution as in base/xartemplates/include/xmlhttprequest.js
 * But now the dojo way with back/forward button support (sort of)
 *
 * @package modules
 * @subpackage dojo
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
var debug = true;

/**
 * Class to keep track of history.
 *
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
function historyEntry(url,tagid){
    this.tagid = tagid; this.url = url;    this.changeUrl = false;
}
historyEntry.prototype.back = function() 
{
    // Load content of what we put on the stack during instantiation
    loadContent(this.url,this.tagid); 
}
historyEntry.prototype.forward = function()
{ 
    loadContent(this.url,this,tagid); 
}


/**
 * Class to keep track of the request
 *
 * @package default
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
function xarRequest(url,tagid) 
{
    var pagepat = /\&pageName\=module/;
    var postfix ='&pageName=module';
    
    // Deviations from prototype
    this.url = url;
    if(url.search(pagepat) == -1) this.url = this.url + postfix;
    this.mimetype  = 'text/xml'
    this.changeUrl = false;
    this.tagid     = tagid; 
}
// Use the dojo request as its prototype
xarRequest.prototype = new dojo.io.Request;
// Make specifics for the the load method
xarRequest.prototype.load = function(type,data,evt)
{
    // Do whatever we need with the content returned
    // Find id in current page
    srcTag = document.getElementById(this.tagid);
    if(srcTag == null) {
        // not found, fallback to normal behaviour
        document.body.style.cursor='default'; // set back to be sure
        document.location = this.url; // TODO: sometimes take out 'pageName' var here
        return true;
    }
    // Make sure we replace the right tag, and dont leave anything behind
    myparent = srcTag.parentNode;
    srcTag.id ='dummytogetridoftheoriginal';
    srcTag.innerHTML = evt.responseText;
    //if(debug) alert(evt.responseText);
    
    // Now, in the modified document, find the tag again, and isolate that.
    newtag = document.getElementById(this.tagid);

    if(newtag == null) {
        if(debug) alert('cant find the new tag [' + this.tagid + ']');
        // put back the original id, so subsequent request can find it.
        srcTag.id = this.tagid;
    } else {
        // Create a copy of that node
        copyofnew = newtag.cloneNode(true);
        // and replace the source tag with that copy
        myparent.replaceChild(copyofnew,srcTag);
    }
    document.body.style.cursor='default';
    return false; // cancel the normal action
}

/**
 * Load a piece of content into a block identified by tagid
 *
 * @return void
 * @author Marcel van der Boom
 **/
function loadContent(url, tagid)
{
    var debug   = false;

    // Create the xar xml http request.
    var req = new xarRequest(url,tagid);

    // Bind the request (i.e. do it)
    var result = dojo.io.bind(req);
    
    // Register the request in the history tracker
    //dojo.undo.browser.addToHistory(historyEntry(url,tagid));
    
    // When result successful, cancel the original
    return !result;
}

