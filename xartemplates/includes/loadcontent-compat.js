/**
 * Similar xmlhttp request solution as in base/xartemplates/include/xmlhttprequest.js
 * But now the dojo way with back/forward button support (sort of)
 *
 * @package modules
 * @subpackage dojo
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
var debug = true;

// Register the initial state in the history stack when the page actually loads
dojo.addOnLoad(function(){
    dojo.undo.browser.setInitialState();//new historyEntry(document.location.href,'rubbishidjustforthefunofit'));
});


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
    
    // Save the original url
    this.orgurl    = url;
    this.tagid     = tagid; 
    
    // Massage url into the one we need to send into the request
    this.url = url; if(url.search(pagepat) == -1) this.url = this.url + postfix;
    
    // Set the rest needed for binding.
    this.mimetype  = 'text/xml'; this.changeUrl = false;
}
// Use the dojo request as its prototype
dojo.inherits(xarRequest,dojo.io.Request);

// Make specifics for the the load method
xarRequest.prototype.load = function(type,data,evt)
{
    // Do whatever we need with the data returned data is delivered as XMLDocument object.
    // Find id in current page, nogo? do a normal request.
    var srcTag = document.getElementById(this.tagid);
    if(srcTag == null) { 
        dojo.debug('The tagid: '+this.tagid+' is not present in current page.');
        document.location = this.orgurl;
        return;
    }
     
    // We do it like this because of: https://bugzilla.mozilla.org/show_bug.cgi?id=252774
    // In short: if there is no DTD info available on what constitutes an ID attribute
    // the getElementById() method wont find anything, so we do this by:
    // 1. inserting the whole response into our original (which is assumed to have a DTD (html has))
    // 2. finding the ID there
    // sucks, but needed.
    // Make sure we replace the right tag, and dont leave anything behind
    srcTag.id ='dummytogetridoftheoriginal';
    srcTag.innerHTML = evt.responseText;
    
    // Now, in the modified document, find the tag again, and isolate that.
    // nogo? put back the orinal id on the tag, and fetch the complete unfiltered
    // result
    var newtag = document.getElementById(this.tagid);

    if(newtag == null) {
        if(debug) dojo.debug('cant find the new tag [' + this.tagid + ']');
        // put back the original id, so subsequent request can find it.
        srcTag.id = this.tagid;
    } else {
        // Filter out the node with the id and just replace that
        copyofnew = newtag.cloneNode(true);
        // and replace the source tag with that copy
        srcTag.parentNode.replaceChild(copyofnew,srcTag);
    }
}
//xarRequest.prototype.back = function() 
//{
//    // Load content of what we put on the stack during instantiation
//    dojo.debug('back for:'+this.tagid+' '+this.url)
//}
//xarRequest.prototype.forward = function() 
//{
//    // Load content of what we put on the stack during instantiation
//    dojo.debug('back for:'+this.tagid+' '+this.url)
//}



/**
 * Load a piece of content into a block identified by tagid
 *
 * @return void
 * @author Marcel van der Boom
 **/
function loadContent(url, tagid, addtoHistory)
{
    document.body.style.cursor='wait';
    
    // Create the xar xml http request.
    var req = new xarRequest(url,tagid);

    // Bind the request (i.e. do it)
    var result = dojo.io.bind(req);
    
    // When result successful, cancel the original
    document.body.style.cursor='default';
    return !result;
}

