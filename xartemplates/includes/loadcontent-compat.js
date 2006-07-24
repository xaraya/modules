/**
 * Similar xmlhttp request solution as in base/xartemplates/include/xmlhttprequest.js
 * But now the dojo way with back/forward button support (sort of)
 *
 * @package modules
 * @subpackage dojo
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/

/**
 * Register the initial state in the history stack when the page actually loads
 * and dojo is finished loading.
 *
 **/
dojo.addOnLoad(
    function()
    {
        dojo.debug("Creating initial history entry");
        dojo.undo.browser.setInitialState(new historyEntry(document.location.href,''));
    }
);

/**
 * Class which stores enough information for a request to be able 
 * to reissue it.
 *
 * @param string url   the url which was requested
 * @param string tagid the id where to put the content retrieved
 * @todo  this class has no notion of context, so if something goes wrong it 
 *        will fallback to a normal http request. This happens for example if
 *        within one page using xmlhttp there are 2 id's which are enabled.
 *        Retrieving the content for one id doesnt mean the other id will be 
 *        present anymore
 *        The back/forward functionality *will* retrieve the right url, but 
 *        the page will be refreshed, loosing other asynchronously retrieved chunks 
 **/
function historyEntry(url,tagid)
{
    // Save what we need to reconstruct
    this.url = url; this.tagid  = tagid; 
    // Needs to be since it wont work with anything but path/like/urls without parameters.
    this.changeUrl = false;
}

historyEntry.prototype.back = 
historyEntry.prototype.forward = function()
{
    //alert('back/forward for:'+this.tagid+' '+this.url);
    // Load up the saved content, but do not add a new historyEntry
    loadContent(this.url, this.tagid, false);
}

/**
 * Class to model a Xaraya specific xmlhttp request.
 *
 * We do a couple of special things besides doing just
 * a request (like massaging the url to use the righ page template)
 * Thus, we use the prototype dojo.io.Request and override what we need
 *
 * @param string url   the url which was requested
 * @param string tagid the id where to put the content retrieved
 * @todo the pageName trick works, but is not very elegant, all the fiddling with id's and stuff comes from it
 *       at some point we will need to train xaraya itself to return chunks of content natively (id from template?)
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

/**
 * Load method is the handler which gets called after the data has been retrieved
 *
 * @param string type 
 * @param mixed  data  object Depending on the request data contains the object returned by the request (e.g. XMLDocument)
 * @param mixed  evt   native evt returned by the request (e.g. XMLHTTPRequest on FF)
 **/
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


/**
 * Load a piece of content into a block identified by tagid
 *
 * @return bool false when loading succeeded, true when it failed, 
 *              returning true means that the default will not get cancelled,
 *              in the case of a link this means that the original link goes through.
 **/
function loadContent(url, tagid, addToHistory)
{
    if(addToHistory == null) addToHistory = true;
    document.body.style.cursor='wait';
    
    // Create the xar xml http request
    var req = new xarRequest(url,tagid);

    // Bind the request (i.e. do it)
    var result = dojo.io.bind(req);
    
    // Add to history
    if(addToHistory) {
        dojo.undo.browser.addToHistory(new historyEntry(url,tagid));
    }
    
    // When result successful, cancel the original
    document.body.style.cursor='default';
    return !result;
}

