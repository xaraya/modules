if(typeof dojo == "undefined"){

/**
* @file bootstrap1.js
*
* summary: First file that is loaded that 'bootstraps' the entire dojo library suite.
* note:  Must run before hostenv_*.js file.
*
* @author  Copyright 2004 Mark D. Anderson (mda@discerning.com)
* TODOC: should the copyright be changed to Dojo Foundation?
* @license Licensed under the Academic Free License 2.1 http://www.opensource.org/licenses/afl-2.1.php
*
* $Id: bootstrap1.js 4883 2006-07-24 01:56:32Z peller $
*/

// TODOC: HOW TO DOC THE BELOW?
// @global: djConfig
// summary:  
//        Application code can set the global 'djConfig' prior to loading
//        the library to override certain global settings for how dojo works.  
// description:  The variables that can be set are as follows:
//            - isDebug: false
//            - allowQueryConfig: false
//            - baseScriptUri: ""
//            - baseRelativePath: ""
//            - libraryScriptUri: ""
//            - iePreventClobber: false
//            - ieClobberMinimal: true
//            - locale: undefined
//            - extraLocale: undefined
//            - preventBackButtonFix: true
//            - searchIds: []
//            - parseWidgets: true
// TODOC: HOW TO DOC THESE VARIABLES?
// TODOC: IS THIS A COMPLETE LIST?
// note:
//        'djConfig' does not exist under 'dojo.*' so that it can be set before the 
//        'dojo' variable exists.  
// note:
//        Setting any of these variables *after* the library has loaded does nothing at all. 
// TODOC: is this still true?  Release notes for 0.3 indicated they could be set after load.
//



//TODOC:  HOW TO DOC THIS?
// @global: dj_global
// summary: 
//        an alias for the top-level global object in the host environment
//        (e.g., the window object in a browser).
// description:  
//        Refer to 'dj_global' rather than referring to window to ensure your
//        code runs correctly in contexts other than web browsers (eg: Rhino on a server).
// TODO: replace this with dojo._currentContext
var dj_global = this;



function dj_undef(/*String*/ name, /*Object?*/ object){
    //summary: Returns true if 'name' is defined on 'object' (or globally if 'object' is null).
    //description: Note that 'defined' and 'exists' are not the same concept.
    
    //Before dojo.global() is defined, object can not be null
    if(object==null){ object = dojo.global(); }
    // exception if object is not an Object
    return (typeof object[name] == "undefined");    // Boolean
}


// make sure djConfig is defined
if(dj_undef("djConfig", this)){ 
    var djConfig = {}; 
}


//TODOC:  HOW TO DOC THIS?
// dojo is the root variable of (almost all) our public symbols -- make sure it is defined.
if(dj_undef("dojo", this)){ 
    var dojo = {}; 
}

//These are private variables, and should not be used; use dojo.global() and dojo.doc() instead
//this variable should probably have a better name
dojo._currentContext = this;
if(!dj_undef("document", dojo._currentContext)){
    dojo._currentDocument = this.document;
}

// Override locale setting, if specified
dojo.locale  = djConfig.locale;

//TODOC:  HOW TO DOC THIS?
dojo.version = {
    // summary: version number of this instance of dojo.
    major: 0, minor: 3, patch: 1, flag: "svn-4896",
    revision: Number("$Rev: 4883 $".match(/[0-9]+/)[0]),
    toString: function(){
        with(dojo.version){
            return major + "." + minor + "." + patch + flag + " (" + revision + ")";    // String
        }
    }
}

dojo.evalProp = function(/*String*/ name, /*Object*/ object, /*Boolean?*/ create){
    // summary: Returns 'object[name]'.  If not defined and 'create' is true, will return a new Object.
    // description: 
    //        Returns null if 'object[name]' is not defined and 'create' is not true.
    //         Note: 'defined' and 'exists' are not the same concept.    
    return (object && !dj_undef(name, object) ? object[name] : (create ? (object[name]={}) : undefined));    // mixed
}


dojo.parseObjPath = function(/*String*/ path, /*Object?*/ context, /*Boolean?*/ create){
    // summary: Parse string path to an object, and return corresponding object reference and property name.
    // description: 
    //        Returns an object with two properties, 'obj' and 'prop'.  
    //        'obj[prop]' is the reference indicated by 'path'.
    // path: Path to an object, in the form "A.B.C".
    // context: Object to use as root of path.  Defaults to 'dj_global'.
    // create: If true, Objects will be created at any point along the 'path' that is undefined.
    var object = (context != null ? context : dj_global);
    var names = path.split('.');
    var prop = names.pop();
    for (var i=0,l=names.length;i<l && object;i++){
        object = dojo.evalProp(names[i], object, create);
    }
    return {obj: object, prop: prop};    // Object: {obj: Object, prop: String}
}


dojo.evalObjPath = function(/*String*/ path, /*Boolean?*/ create){
    // summary: Return the value of object at 'path' in the global scope, without using 'eval()'.
    // path: Path to an object, in the form "A.B.C".
    // create: If true, Objects will be created at any point along the 'path' that is undefined.
    if(typeof path != "string"){ 
        return dj_global; 
    }
    // fast path for no periods
    if(path.indexOf('.') == -1){
        return dojo.evalProp(path, dj_global, create);        // mixed
    }

    //MOW: old 'with' syntax was confusing and would throw an error if parseObjPath returned null.
    var ref = dojo.parseObjPath(path, dj_global, create);
    if(ref){
        return dojo.evalProp(ref.prop, ref.obj, create);    // mixed
    }
    return null;
}

// ****************************************************************
// global public utils
// TODOC: DO WE WANT TO NOTE THAT THESE ARE GLOBAL PUBLIC UTILS?
// ****************************************************************

dojo.global = function(){
    // summary:
    //        return the top-level global object in the host environment
    //        (e.g., the window object in a browser).
    // description: 
    //        Refer to 'dojo.global()' rather than referring to window to ensure your
    //        code runs correctly in contexts other than web browsers (eg: Rhino on a server).
    return dojo._currentContext;
}

dojo.doc = function(){
    // summary:
    //        return the document object associated with the dojo.global()
    return dojo._currentDocument;
}

dojo.body  = function(){
    // summary:
    //        return the body object associated with dojo.doc()
    // Note: document.body is not defined for a strict xhtml document
    return dojo.doc().body || dojo.doc().getElementsByTagName("body")[0];
}

dojo.withGlobal = function(/*Object*/globalObject, /*Function*/callback, /*Object?*/thisObject /* ... */){
    // summary:
    //        Call callback with globalObject as dojo.global() and globalObject.document 
    //        as dojo.doc(), if provided, globalObject will be executed in the context of 
    //        object thisObject
    // description: 
    //        When callback() returns or throws an error, the dojo.global() and dojo.doc() will
    //        be restored to its previous state.
    var oldDoc = dojo._currentDocument;
    var oldWin = dojo._currentContext;
    var rval;
    try{
        dojo._currentContext = globalObject;
        dojo._currentDocument = globalObject.document;
        if(thisObject){ rval = dojo.lang.curryArguments(thisObject, callback, arguments, 3); }
        else{ rval = callback(); }
    } catch(e) {
        dojo._currentContext = oldWin;
        dojo._currentDocument = oldDoc;
        throw e;
    }
    dojo._currentContext = oldWin;
    dojo._currentDocument = oldDoc;
    return rval;
}

dojo.withDoc = function (/*Object*/globalObject, /*Function*/callback, /*Object?*/thisObject /* ... */) {
    // summary:
    //        Call callback with globalObject as dojo.doc(), if provided, callback will be executed
    //        in the context of object thisObject
    // description: 
    //        When callback() returns or throws an error, the dojo.doc() will
    //        be restored to its previous state.
    var oldDoc = this._currentDocument;
    var rval;
    try{
        dojo._currentDocument = globalObject;
        if(thisObject){ rval = dojo.lang.curryArguments(thisObject, callback, arguments, 3); }
        else{ rval = callback(); }
    } catch(e) {
        dojo._currentDocument = oldDoc;
        throw e;
    }
    dojo._currentDocument = oldDoc;
    return rval;
}

dojo.errorToString = function(/*Error*/ exception){
    // summary: Return an exception's 'message', 'description' or text.

    // TODO: overriding Error.prototype.toString won't accomplish this?
     //         ... since natively generated Error objects do not always reflect such things?
    if(!dj_undef("message", exception)){
        return exception.message;        // String
    }else if(!dj_undef("description", exception)){
        return exception.description;    // String
    }else{
        return exception;                // Error
    }
}


dojo.raise = function(/*String*/ message, /*Error?*/ exception){
    // summary: Throw an error message, appending text of 'exception' if provided.
    // note: Also prints a message to the user using 'dojo.hostenv.println'.
    if(exception){
        message = message + ": "+dojo.errorToString(exception);
    }

    // print the message to the user if hostenv.println is defined
    try {    dojo.hostenv.println("FATAL: "+message); } catch (e) {}

    throw Error(message);
}

//Stub functions so things don't break.
//TODOC:  HOW TO DOC THESE?
dojo.debug = function(){}
dojo.debugShallow = function(obj){}
dojo.profile = { start: function(){}, end: function(){}, stop: function(){}, dump: function(){} };


function dj_eval(/*String*/ scriptFragment){ 
    // summary: Perform an evaluation in the global scope.  Use this rather than calling 'eval()' directly.
    // description: Placed in a separate function to minimize size of trapped evaluation context.
    // note:
    //     - JSC eval() takes an optional second argument which can be 'unsafe'.
    //     - Mozilla/SpiderMonkey eval() takes an optional second argument which is the
    //       scope object for new symbols.
    return dj_global.eval ? dj_global.eval(scriptFragment) : eval(scriptFragment);     // mixed
}



dojo.unimplemented = function(/*String*/ funcname, /*String?*/ extra){
    // summary: Throw an exception because some function is not implemented.
    // extra: Text to append to the exception message.
    var message = "'" + funcname + "' not implemented";
    if (extra != null) { message += " " + extra; }
    dojo.raise(message);
}


dojo.deprecated = function(/*String*/ behaviour, /*String?*/ extra, /*String?*/ removal){
    // summary: Log a debug message to indicate that a behavior has been deprecated.
    // extra: Text to append to the message.
    // removal: Text to indicate when in the future the behavior will be removed.
    var message = "DEPRECATED: " + behaviour;
    if(extra){ message += " " + extra; }
    if(removal){ message += " -- will be removed in version: " + removal; }
    dojo.debug(message);
}



dojo.inherits = function(/*Function*/ subclass, /*Function*/ superclass){
    // summary: Set up inheritance between two classes.
    if(typeof superclass != 'function'){ 
        dojo.raise("dojo.inherits: superclass argument ["+superclass+"] must be a function (subclass: [" + subclass + "']");
    }
    subclass.prototype = new superclass();
    subclass.prototype.constructor = subclass;
    subclass.superclass = superclass.prototype;
    // DEPRICATED: super is a reserved word, use 'superclass'
    subclass['super'] = superclass.prototype;
}

dojo.render = (function(){
    //TODOC: HOW TO DOC THIS?
    // summary: Details rendering support, OS and browser of the current environment.
    // TODOC: is this something many folks will interact with?  If so, we should doc the structure created...
    function vscaffold(prefs, names){
        var tmp = {
            capable: false,
            support: {
                builtin: false,
                plugin: false
            },
            prefixes: prefs
        };
        for(var i=0; i<names.length; i++){
            tmp[names[i]] = false;
        }
        return tmp;
    }

    return {
        name: "",
        ver: dojo.version,
        os: { win: false, linux: false, osx: false },
        html: vscaffold(["html"], ["ie", "opera", "khtml", "safari", "moz"]),
        svg: vscaffold(["svg"], ["corel", "adobe", "batik"]),
        vml: vscaffold(["vml"], ["ie"]),
        swf: vscaffold(["Swf", "Flash", "Mm"], ["mm"]),
        swt: vscaffold(["Swt"], ["ibm"])
    };
})();

// ****************************************************************
// dojo.hostenv methods that must be defined in hostenv_*.js
// ****************************************************************

/**
 * The interface definining the interaction with the EcmaScript host environment.
*/

/*
 * None of these methods should ever be called directly by library users.
 * Instead public methods such as loadModule should be called instead.
 */
dojo.hostenv = (function(){
    // TODOC:  HOW TO DOC THIS?
    // summary: Provides encapsulation of behavior that changes across different 'host environments' 
    //            (different browsers, server via Rhino, etc).
    // description: None of these methods should ever be called directly by library users.
    //                Use public methods such as 'loadModule' instead.
    
    // default configuration options
    var config = {
        isDebug: false,
        allowQueryConfig: false,
        baseScriptUri: "",
        baseRelativePath: "",
        libraryScriptUri: "",
        iePreventClobber: false,
        ieClobberMinimal: true,
        preventBackButtonFix: true,
        searchIds: [],
        parseWidgets: true
    };

    if (typeof djConfig == "undefined") { djConfig = config; }
    else {
        for (var option in config) {
            if (typeof djConfig[option] == "undefined") {
                djConfig[option] = config[option];
            }
        }
    }

    return {
        name_: '(unset)',
        version_: '(unset)',


        getName: function(){ 
            // sumary: Return the name of the host environment.
            return this.name_;     // String
        },


        getVersion: function(){ 
            // summary: Return the version of the hostenv.
            return this.version_; // String
        },

        getText: function(/*String*/ uri){
            // summary:    Read the plain/text contents at the specified 'uri'.
            // description: 
            //            If 'getText()' is not implemented, then it is necessary to override 
            //            'loadUri()' with an implementation that doesn't rely on it.

            dojo.unimplemented('getText', "uri=" + uri);
        }
    };
})();


dojo.hostenv.getBaseScriptUri = function(){
    // summary: Return the base script uri that other scripts are found relative to.
    // TODOC: HUH?  This comment means nothing to me.  What other scripts? Is this the path to other dojo libraries?
    //        MAYBE:  Return the base uri to scripts in the dojo library.     ???
    // return: Empty string or a path ending in '/'.
    if(djConfig.baseScriptUri.length){ 
        return djConfig.baseScriptUri;
    }

    // MOW: Why not:
    //            uri = djConfig.libraryScriptUri || djConfig.baseRelativePath
    //        ??? Why 'new String(...)'
    var uri = new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
    if (!uri) { dojo.raise("Nothing returned by getLibraryScriptUri(): " + uri); }

    // MOW: uri seems to not be actually used.  Seems to be hard-coding to djConfig.baseRelativePath... ???
    var lastslash = uri.lastIndexOf('/');        // MOW ???
    djConfig.baseScriptUri = djConfig.baseRelativePath;
    return djConfig.baseScriptUri;    // String
}

/*
 * loader.js - runs before the hostenv_*.js file. Contains all of the package loading methods.
 */

//A semi-colon is at the start of the line because after doing a build, this function definition
//get compressed onto the same line as the last line in bootstrap1.js. That list line is just a
//curly bracket, and the browser complains about that syntax. The semicolon fixes it. Putting it
//here instead of at the end of bootstrap1.js, since it is more of an issue for this file, (using
//the closure), and bootstrap1.js could change in the future.
;(function(){
    //Additional properties for dojo.hostenv
    var _addHostEnv = {
        pkgFileName: "__package__",
    
        // for recursion protection
        loading_modules_: {},
        loaded_modules_: {},
        addedToLoadingCount: [],
        removedFromLoadingCount: [],
    
        inFlightCount: 0,
    
        // FIXME: it should be possible to pull module prefixes in from djConfig
        modulePrefixes_: {
            dojo: {name: "dojo", value: "src"}
        },
    
    
        setModulePrefix: function(module, prefix){
            this.modulePrefixes_[module] = {name: module, value: prefix};
        },
    
        getModulePrefix: function(module){
            var mp = this.modulePrefixes_;
            if((mp[module])&&(mp[module]["name"])){
                return mp[module].value;
            }
            return module;
        },

        getTextStack: [],
        loadUriStack: [],
        loadedUris: [],
    
        //WARNING: This variable is referenced by packages outside of bootstrap: FloatingPane.js and undo/browser.js
        post_load_: false,
        
        //Egad! Lots of test files push on this directly instead of using dojo.addOnLoad.
        modulesLoadedListeners: [],
        unloadListeners: [],
        loadNotifying: false
    };
    
    //Add all of these properties to dojo.hostenv
    for(var param in _addHostEnv){
        dojo.hostenv[param] = _addHostEnv[param];
    }
})();

/**
 * Loads and interprets the script located at relpath, which is relative to the
 * script root directory.  If the script is found but its interpretation causes
 * a runtime exception, that exception is not caught by us, so the caller will
 * see it.  We return a true value if and only if the script is found.
 *
 * For now, we do not have an implementation of a true search path.  We
 * consider only the single base script uri, as returned by getBaseScriptUri().
 *
 * @param relpath A relative path to a script (no leading '/', and typically
 * ending in '.js').
 * @param module A module whose existance to check for after loading a path.
 * Can be used to determine success or failure of the load.
 * @param cb a function to pass the result of evaluating the script (optional)
 */
dojo.hostenv.loadPath = function(relpath, module /*optional*/, cb /*optional*/){
    var uri;
    if((relpath.charAt(0) == '/')||(relpath.match(/^\w+:/))){
        // dojo.raise("relpath '" + relpath + "'; must be relative");
        uri = relpath;
    }else{
        uri = this.getBaseScriptUri() + relpath;
    }
    if(djConfig.cacheBust && dojo.render.html.capable){
        uri += "?" + String(djConfig.cacheBust).replace(/\W+/g,"");
    }
    try{
        return ((!module) ? this.loadUri(uri, cb) : this.loadUriAndCheck(uri, module, cb));
    }catch(e){
        dojo.debug(e);
        return false;
    }
}

/**
 * Reads the contents of the URI, and evaluates the contents.
 * Returns true if it succeeded. Returns false if the URI reading failed.
 * Throws if the evaluation throws.
 * The result of the eval is not available to the caller TODO: now it is; was this a deliberate restriction?
 *
 * @param uri a uri which points at the script to be loaded
 * @param cb a function to process the result of evaluating the script as an expression (optional)
 */
dojo.hostenv.loadUri = function(uri, cb /*optional*/){
    if(this.loadedUris[uri]){
        return 1;
    }
    var contents = this.getText(uri, null, true);
    if(contents == null){ return 0; }
    this.loadedUris[uri] = true;
    if(cb){ contents = '('+contents+')'; }
    var value = dj_eval(contents);
    if(cb){
        cb(value);
    }
    return 1;
}

// FIXME: probably need to add logging to this method
dojo.hostenv.loadUriAndCheck = function(uri, module, cb){
    var ok = true;
    try{
        ok = this.loadUri(uri, cb);
    }catch(e){
        dojo.debug("failed loading ", uri, " with error: ", e);
    }
    return ((ok)&&(this.findModule(module, false))) ? true : false;
}

dojo.loaded = function(){ }
dojo.unloaded = function(){ }

dojo.hostenv.loaded = function(){
    this.loadNotifying = true;
    this.post_load_ = true;
    var mll = this.modulesLoadedListeners;
    for(var x=0; x<mll.length; x++){
        mll[x]();
    }

    //Clear listeners so new ones can be added
    //For other xdomain package loads after the initial load.
    this.modulesLoadedListeners = [];
    this.loadNotifying = false;

    dojo.loaded();
}

dojo.hostenv.unloaded = function(){
    var mll = this.unloadListeners;
    while(mll.length){
        (mll.pop())();
    }
    dojo.unloaded();
}

/*
Call styles:
    dojo.addOnLoad(functionPointer)
    dojo.addOnLoad(object, "functionName")
*/
dojo.addOnLoad = function(obj, fcnName) {
    var dh = dojo.hostenv;
    if(arguments.length == 1) {
        dh.modulesLoadedListeners.push(obj);
    } else if(arguments.length > 1) {
        dh.modulesLoadedListeners.push(function() {
            obj[fcnName]();
        });
    }

    //Added for xdomain loading. dojo.addOnLoad is used to
    //indicate callbacks after doing some dojo.require() statements.
    //In the xdomain case, if all the requires are loaded (after initial
    //page load), then immediately call any listeners.
    if(dh.post_load_ && dh.inFlightCount == 0 && !dh.loadNotifying){
        dh.callLoaded();
    }
}

dojo.addOnUnload = function(obj, fcnName){
    var dh = dojo.hostenv;
    if(arguments.length == 1){
        dh.unloadListeners.push(obj);
    } else if(arguments.length > 1) {
        dh.unloadListeners.push(function() {
            obj[fcnName]();
        });
    }
}

dojo.hostenv.modulesLoaded = function(){
    if(this.post_load_){ return; }
    if((this.loadUriStack.length==0)&&(this.getTextStack.length==0)){
        if(this.inFlightCount > 0){ 
            dojo.debug("files still in flight!");
            return;
        }
        dojo.hostenv.callLoaded();
    }
}

dojo.hostenv.callLoaded = function(){
    if(typeof setTimeout == "object"){
        setTimeout("dojo.hostenv.loaded();", 0);
    }else{
        dojo.hostenv.loaded();
    }
}

dojo.hostenv.getModuleSymbols = function(modulename) {
    var syms = modulename.split(".");
    for(var i = syms.length - 1; i > 0; i--){
        var parentModule = syms.slice(0, i).join(".");
        var parentModulePath = this.getModulePrefix(parentModule);
        if(parentModulePath != parentModule){
            syms.splice(0, i, parentModulePath);
            break;
        }
    }
    return syms;
}

/**
* loadModule("A.B") first checks to see if symbol A.B is defined. 
* If it is, it is simply returned (nothing to do).
*
* If it is not defined, it will look for "A/B.js" in the script root directory,
* followed by "A.js".
*
* It throws if it cannot find a file to load, or if the symbol A.B is not
* defined after loading.
*
* It returns the object A.B.
*
* This does nothing about importing symbols into the current package.
* It is presumed that the caller will take care of that. For example, to import
* all symbols:
*
*    with (dojo.hostenv.loadModule("A.B")) {
*       ...
*    }
*
* And to import just the leaf symbol:
*
*    var B = dojo.hostenv.loadModule("A.B");
*    ...
*
* dj_load is an alias for dojo.hostenv.loadModule
*/
dojo.hostenv._global_omit_module_check = false;
dojo.hostenv.loadModule = function(modulename, exact_only, omit_module_check){
    if(!modulename){ return; }
    omit_module_check = this._global_omit_module_check || omit_module_check;
    var module = this.findModule(modulename, false);
    if(module){
        return module;
    }

    // protect against infinite recursion from mutual dependencies
    if(dj_undef(modulename, this.loading_modules_)){
        this.addedToLoadingCount.push(modulename);
    }
    this.loading_modules_[modulename] = 1;

    // convert periods to slashes
    var relpath = modulename.replace(/\./g, '/') + '.js';

    var nsyms = modulename.split(".");
    if(djConfig.autoLoadNamespace){ dojo.getNamespace(nsyms[0]); }

    var syms = this.getModuleSymbols(modulename);
    var startedRelative = ((syms[0].charAt(0) != '/')&&(!syms[0].match(/^\w+:/)));
    var last = syms[syms.length - 1];
    // figure out if we're looking for a full package, if so, we want to do
    // things slightly diffrently
    if(last=="*"){
        modulename = (nsyms.slice(0, -1)).join('.');

        while(syms.length){
            syms.pop();
            syms.push(this.pkgFileName);
            relpath = syms.join("/") + '.js';
            if(startedRelative && (relpath.charAt(0)=="/")){
                relpath = relpath.slice(1);
            }
            ok = this.loadPath(relpath, ((!omit_module_check) ? modulename : null));
            if(ok){ break; }
            syms.pop();
        }
    }else{
        relpath = syms.join("/") + '.js';
        modulename = nsyms.join('.');
        var ok = this.loadPath(relpath, ((!omit_module_check) ? modulename : null));
        if((!ok)&&(!exact_only)){
            syms.pop();
            while(syms.length){
                relpath = syms.join('/') + '.js';
                ok = this.loadPath(relpath, ((!omit_module_check) ? modulename : null));
                if(ok){ break; }
                syms.pop();
                relpath = syms.join('/') + '/'+this.pkgFileName+'.js';
                if(startedRelative && (relpath.charAt(0)=="/")){
                    relpath = relpath.slice(1);
                }
                ok = this.loadPath(relpath, ((!omit_module_check) ? modulename : null));
                if(ok){ break; }
            }
        }

        if((!ok)&&(!omit_module_check)){
            dojo.raise("Could not load '" + modulename + "'; last tried '" + relpath + "'");
        }
    }

    // check that the symbol was defined
    //Don't bother if we're doing xdomain (asynchronous) loading.
    if(!omit_module_check && !this["isXDomain"]){
        // pass in false so we can give better error
        module = this.findModule(modulename, false);
        if(!module){
            dojo.raise("symbol '" + modulename + "' is not defined after loading '" + relpath + "'"); 
        }
    }

    return module;
}

/**
* startPackage("A.B") follows the path, and at each level creates a new empty
* object or uses what already exists. It returns the result.
*/
dojo.hostenv.startPackage = function(packname){
    var modref = dojo.evalObjPath((packname.split(".").slice(0, -1)).join('.'));
    this.loaded_modules_[(new String(packname)).toLowerCase()] = modref;

    var syms = packname.split(/\./);
    if(syms[syms.length-1]=="*"){
        syms.pop();
    }
    return dojo.evalObjPath(syms.join("."), true);
}

/**
 * findModule("A.B") returns the object A.B if it exists, otherwise null.
 * @param modulename A string like 'A.B'.
 * @param must_exist Optional, defualt false. throw instead of returning null
 * if the module does not currently exist.
 */
dojo.hostenv.findModule = function(modulename, must_exist){
    // check cache
    /*
    if(!dj_undef(modulename, this.modules_)){
        return this.modules_[modulename];
    }
    */

    var lmn = (new String(modulename)).toLowerCase();

    if(this.loaded_modules_[lmn]){
        return this.loaded_modules_[lmn];
    }

    // see if symbol is defined anyway
    var module = dojo.evalObjPath(modulename);
    if((modulename)&&(typeof module != 'undefined')&&(module)){
        this.loaded_modules_[lmn] = module;
        return module;
    }

    if(must_exist){
        dojo.raise("no loaded module named '" + modulename + "'");
    }
    return null;
}

//Start of old bootstrap2:

/*
 * This method taks a "map" of arrays which one can use to optionally load dojo
 * modules. The map is indexed by the possible dojo.hostenv.name_ values, with
 * two additional values: "default" and "common". The items in the "default"
 * array will be loaded if none of the other items have been choosen based on
 * the hostenv.name_ item. The items in the "common" array will _always_ be
 * loaded, regardless of which list is chosen.  Here's how it's normally
 * called:
 *
 *    dojo.kwCompoundRequire({
 *        browser: [
 *            ["foo.bar.baz", true, true], // an example that passes multiple args to loadModule()
 *            "foo.sample.*",
 *            "foo.test,
 *        ],
 *        default: [ "foo.sample.*" ],
 *        common: [ "really.important.module.*" ]
 *    });
 */
dojo.kwCompoundRequire = function(modMap){
    var common = modMap["common"]||[];
    var result = (modMap[dojo.hostenv.name_]) ? common.concat(modMap[dojo.hostenv.name_]||[]) : common.concat(modMap["default"]||[]);

    for(var x=0; x<result.length; x++){
        var curr = result[x];
        if(curr.constructor == Array){
            dojo.hostenv.loadModule.apply(dojo.hostenv, curr);
        }else{
            dojo.hostenv.loadModule(curr);
        }
    }
}

dojo.require = function(){
    dojo.hostenv.loadModule.apply(dojo.hostenv, arguments);
}

dojo.requireIf = function(){
    if((arguments[0] === true)||(arguments[0]=="common")||(arguments[0] && dojo.render[arguments[0]].capable)){
        var args = [];
        for (var i = 1; i < arguments.length; i++) { args.push(arguments[i]); }
        dojo.require.apply(dojo, args);
    }
}

dojo.requireAfterIf = dojo.requireIf;

dojo.provide = function(){
    return dojo.hostenv.startPackage.apply(dojo.hostenv, arguments);
}

dojo.setModulePrefix = function(module, prefix){
    return dojo.hostenv.setModulePrefix(module, prefix);
}

// determine if an object supports a given method
// useful for longer api chains where you have to test each object in the chain
dojo.exists = function(obj, name){
    var p = name.split(".");
    for(var i = 0; i < p.length; i++){
        if(!(obj[p[i]])){ return false; }
        obj = obj[p[i]];
    }
    return true;
}

};

if(typeof window == 'undefined'){
    dojo.raise("no window object");
}

// attempt to figure out the path to dojo if it isn't set in the config
(function() {
    // before we get any further with the config options, try to pick them out
    // of the URL. Most of this code is from NW
    if(djConfig.allowQueryConfig){
        var baseUrl = document.location.toString(); // FIXME: use location.query instead?
        var params = baseUrl.split("?", 2);
        if(params.length > 1){
            var paramStr = params[1];
            var pairs = paramStr.split("&");
            for(var x in pairs){
                var sp = pairs[x].split("=");
                // FIXME: is this eval dangerous?
                if((sp[0].length > 9)&&(sp[0].substr(0, 9) == "djConfig.")){
                    var opt = sp[0].substr(9);
                    try{
                        djConfig[opt]=eval(sp[1]);
                    }catch(e){
                        djConfig[opt]=sp[1];
                    }
                }
            }
        }
    }

    if(((djConfig["baseScriptUri"] == "")||(djConfig["baseRelativePath"] == "")) &&(document && document.getElementsByTagName)){
        var scripts = document.getElementsByTagName("script");
        var rePkg = /(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
        for(var i = 0; i < scripts.length; i++) {
            var src = scripts[i].getAttribute("src");
            if(!src) { continue; }
            var m = src.match(rePkg);
            if(m) {
                var root = src.substring(0, m.index);
                if(src.indexOf("bootstrap1") > -1) { root += "../"; }
                if(!this["djConfig"]) { djConfig = {}; }
                if(djConfig["baseScriptUri"] == "") { djConfig["baseScriptUri"] = root; }
                if(djConfig["baseRelativePath"] == "") { djConfig["baseRelativePath"] = root; }
                break;
            }
        }
    }

    // fill in the rendering support information in dojo.render.*
    var dr = dojo.render;
    var drh = dojo.render.html;
    var drs = dojo.render.svg;
    var dua = (drh.UA = navigator.userAgent);
    var dav = (drh.AV = navigator.appVersion);
    var t = true;
    var f = false;
    drh.capable = t;
    drh.support.builtin = t;

    dr.ver = parseFloat(drh.AV);
    dr.os.mac = dav.indexOf("Macintosh") >= 0;
    dr.os.win = dav.indexOf("Windows") >= 0;
    // could also be Solaris or something, but it's the same browser
    dr.os.linux = dav.indexOf("X11") >= 0;

    drh.opera = dua.indexOf("Opera") >= 0;
    drh.khtml = (dav.indexOf("Konqueror") >= 0)||(dav.indexOf("Safari") >= 0);
    drh.safari = dav.indexOf("Safari") >= 0;
    var geckoPos = dua.indexOf("Gecko");
    drh.mozilla = drh.moz = (geckoPos >= 0)&&(!drh.khtml);
    if (drh.mozilla) {
        // gecko version is YYYYMMDD
        drh.geckoVersion = dua.substring(geckoPos + 6, geckoPos + 14);
    }
    drh.ie = (document.all)&&(!drh.opera);
    drh.ie50 = drh.ie && dav.indexOf("MSIE 5.0")>=0;
    drh.ie55 = drh.ie && dav.indexOf("MSIE 5.5")>=0;
    drh.ie60 = drh.ie && dav.indexOf("MSIE 6.0")>=0;
    drh.ie70 = drh.ie && dav.indexOf("MSIE 7.0")>=0;

    // TODO: is the HTML LANG attribute relevant?
    dojo.locale = dojo.locale || (drh.ie ? navigator.userLanguage : navigator.language).toLowerCase();

    dr.vml.capable=drh.ie;
    drs.capable = f;
    drs.support.plugin = f;
    drs.support.builtin = f;
    if (document.implementation
        && document.implementation.hasFeature
        && document.implementation.hasFeature("org.w3c.dom.svg", "1.0")){
        drs.capable = t;
        drs.support.builtin = t;
        drs.support.plugin = f;
    }
})();

dojo.hostenv.startPackage("dojo.hostenv");

dojo.render.name = dojo.hostenv.name_ = 'browser';
dojo.hostenv.searchIds = [];

// These are in order of decreasing likelihood; this will change in time.
dojo.hostenv._XMLHTTP_PROGIDS = ['Msxml2.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.4.0'];

dojo.hostenv.getXmlhttpObject = function(){
    var http = null;
    var last_e = null;
    try{ http = new XMLHttpRequest(); }catch(e){}
    if(!http){
        for(var i=0; i<3; ++i){
            var progid = dojo.hostenv._XMLHTTP_PROGIDS[i];
            try{
                http = new ActiveXObject(progid);
            }catch(e){
                last_e = e;
            }

            if(http){
                dojo.hostenv._XMLHTTP_PROGIDS = [progid];  // so faster next time
                break;
            }
        }

        /*if(http && !http.toString) {
            http.toString = function() { "[object XMLHttpRequest]"; }
        }*/
    }

    if(!http){
        return dojo.raise("XMLHTTP not available", last_e);
    }

    return http;
}

/**
 * Read the contents of the specified uri and return those contents.
 *
 * @param uri A relative or absolute uri. If absolute, it still must be in the
 * same "domain" as we are.
 *
 * @param async_cb If not specified, load synchronously. If specified, load
 * asynchronously, and use async_cb as the progress handler which takes the
 * xmlhttp object as its argument. If async_cb, this function returns null.
 *
 * @param fail_ok Default false. If fail_ok and !async_cb and loading fails,
 * return null instead of throwing.
 */
dojo.hostenv.getText = function(uri, async_cb, fail_ok){

    var http = this.getXmlhttpObject();

    function isDocumentOk(http){
        var stat = http["status"];
        // allow a 304 use cache, needed in konq (is this compliant with the http spec?)
        return Boolean((!stat)||((200 <= stat)&&(300 > stat))||(stat==304));
    }

    if(async_cb){
        http.onreadystatechange = function(){
            if(4==http.readyState){
                if(isDocumentOk(http)){
                    // dojo.debug("LOADED URI: "+uri);
                    async_cb(http.responseText);
                }
            }
        }
    }

    http.open('GET', uri, async_cb ? true : false);
    try{
        http.send(null);
        if(async_cb){
            return null;
        }
        if(!isDocumentOk(http)){
            var err = Error("Unable to load "+uri+" status:"+ http.status);
            err.status = http.status;
            err.responseText = http.responseText;
            throw err;
        }
    }catch(e){
        if((fail_ok)&&(!async_cb)){
            return null;
        }else{
            throw e;
        }
    }

    return http.responseText;
}

/*
 * It turns out that if we check *right now*, as this script file is being loaded,
 * then the last script element in the window DOM is ourselves.
 * That is because any subsequent script elements haven't shown up in the document
 * object yet.
 */
 /*
function dj_last_script_src() {
    var scripts = window.document.getElementsByTagName('script');
    if(scripts.length < 1){
        dojo.raise("No script elements in window.document, so can't figure out my script src");
    }
    var script = scripts[scripts.length - 1];
    var src = script.src;
    if(!src){
        dojo.raise("Last script element (out of " + scripts.length + ") has no src");
    }
    return src;
}

if(!dojo.hostenv["library_script_uri_"]){
    dojo.hostenv.library_script_uri_ = dj_last_script_src();
}
*/

dojo.hostenv.defaultDebugContainerId = 'dojoDebug';
dojo.hostenv._println_buffer = [];
dojo.hostenv._println_safe = false;
dojo.hostenv.println = function (line){
    if(!dojo.hostenv._println_safe){
        dojo.hostenv._println_buffer.push(line);
    }else{
        try {
            var console = document.getElementById(djConfig.debugContainerId ?
                djConfig.debugContainerId : dojo.hostenv.defaultDebugContainerId);
            if(!console) { console = dojo.body(); }

            var div = document.createElement("div");
            div.appendChild(document.createTextNode(line));
            console.appendChild(div);
        } catch (e) {
            try{
                // safari needs the output wrapped in an element for some reason
                document.write("<div>" + line + "</div>");
            }catch(e2){
                window.status = line;
            }
        }
    }
}

dojo.addOnLoad(function(){
    dojo.hostenv._println_safe = true;
    while(dojo.hostenv._println_buffer.length > 0){
        dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
    }
});

function dj_addNodeEvtHdlr(node, evtName, fp, capture){
    var oldHandler = node["on"+evtName] || function(){};
    node["on"+evtName] = function(){
        fp.apply(node, arguments);
        oldHandler.apply(node, arguments);
    }
    return true;
}


/* Uncomment this to allow init after DOMLoad, not after window.onload

// Mozilla exposes the event we could use
if (dojo.render.html.mozilla) {
   document.addEventListener("DOMContentLoaded", dj_load_init, null);
}
// for Internet Explorer. readyState will not be achieved on init call, but dojo doesn't need it
//Tighten up the comments below to allow init after DOMLoad, not after window.onload
/ * @cc_on @ * /
/ * @if (@_win32)
    document.write("<script defer>dj_load_init()<"+"/script>");
/ * @end @ * /
*/

// default for other browsers
// potential TODO: apply setTimeout approach for other browsers
// that will cause flickering though ( document is loaded and THEN is processed)
// maybe show/hide required in this case..
// TODO: other browsers may support DOMContentLoaded/defer attribute. Add them to above.
dj_addNodeEvtHdlr(window, "load", function(){
    // allow multiple calls, only first one will take effect
    if(arguments.callee.initialized){ return; }
    arguments.callee.initialized = true;

    var initFunc = function(){
        //perform initialization
        if(dojo.render.html.ie){
            dojo.hostenv.makeWidgets();
        }
    };

    if(dojo.hostenv.inFlightCount == 0){
        initFunc();
        dojo.hostenv.modulesLoaded();
    }else{
        dojo.addOnLoad(initFunc);
    }
});

dj_addNodeEvtHdlr(window, "unload", function(){
    dojo.hostenv.unloaded();
});

dojo.hostenv.makeWidgets = function(){
    // you can put searchIds in djConfig and dojo.hostenv at the moment
    // we should probably eventually move to one or the other
    var sids = [];
    if(djConfig.searchIds && djConfig.searchIds.length > 0) {
        sids = sids.concat(djConfig.searchIds);
    }
    if(dojo.hostenv.searchIds && dojo.hostenv.searchIds.length > 0) {
        sids = sids.concat(dojo.hostenv.searchIds);
    }

    if((djConfig.parseWidgets)||(sids.length > 0)){
        if(dojo.evalObjPath("dojo.widget.Parse")){
            // we must do this on a delay to avoid:
            //    http://www.shaftek.org/blog/archives/000212.html
            // IE is such a tremendous peice of shit.
                var parser = new dojo.xml.Parse();
                if(sids.length > 0){
                    for(var x=0; x<sids.length; x++){
                        var tmpNode = document.getElementById(sids[x]);
                        if(!tmpNode){ continue; }
                        var frag = parser.parseElement(tmpNode, null, true);
                        dojo.widget.getParser().createComponents(frag);
                    }
                }else if(djConfig.parseWidgets){
                    var frag  = parser.parseElement(dojo.body(), null, true);
                    dojo.widget.getParser().createComponents(frag);
                }
        }
    }
}

dojo.addOnLoad(function(){
    if(!dojo.render.html.ie) {
        dojo.hostenv.makeWidgets();
    }
});

try {
    if (dojo.render.html.ie) {
        document.namespaces.add("v","urn:schemas-microsoft-com:vml");
        document.createStyleSheet().addRule("v\\:*", "behavior:url(#default#VML)");
    }
} catch (e) { }

// stub, over-ridden by debugging code. This will at least keep us from
// breaking when it's not included
dojo.hostenv.writeIncludes = function(){}

dojo.byId = function(id, doc){
    if(id && (typeof id == "string" || id instanceof String)){
        if(!doc){ doc = dojo.doc(); }
        return doc.getElementById(id);
    }
    return id; // assume it's a node
}

//Semicolon is for when this file is integrated with a custom build on one line
//with some other file's contents. Sometimes that makes things not get defined
//properly, particularly with the using the closure below to do all the work.
;(function(){
    //Don't do this work if dojo.js has already done it.
    if(typeof dj_usingBootstrap != "undefined"){
        return;
    }

    var isRhino = false;
    var isSpidermonkey = false;
    var isDashboard = false;
    if((typeof this["load"] == "function")&&((typeof this["Packages"] == "function")||(typeof this["Packages"] == "object"))){
        isRhino = true;
    }else if(typeof this["load"] == "function"){
        isSpidermonkey  = true;
    }else if(window.widget){
        isDashboard = true;
    }

    var tmps = [];
    if((this["djConfig"])&&((djConfig["isDebug"])||(djConfig["debugAtAllCosts"]))){
        tmps.push("debug.js");
    }

    if((this["djConfig"])&&(djConfig["debugAtAllCosts"])&&(!isRhino)&&(!isDashboard)){
        tmps.push("browser_debug.js");
    }

    //Support compatibility packages. Right now this only allows setting one
    //compatibility package. Might need to revisit later down the line to support
    //more than one.
    if((this["djConfig"])&&(djConfig["compat"])){
        tmps.push("compat/" + djConfig["compat"] + ".js");
    }

    var loaderRoot = djConfig["baseScriptUri"];
    if((this["djConfig"])&&(djConfig["baseLoaderUri"])){
        loaderRoot = djConfig["baseLoaderUri"];
    }

    for(var x=0; x < tmps.length; x++){
        var spath = loaderRoot+"src/"+tmps[x];
        if(isRhino||isSpidermonkey){
            load(spath);
        } else {
            try {
                document.write("<scr"+"ipt type='text/javascript' src='"+spath+"'></scr"+"ipt>");
            } catch (e) {
                var script = document.createElement("script");
                script.src = spath;
                document.getElementsByTagName("head")[0].appendChild(script);
            }
        }
    }
})();

// Localization routines

/**
 * The locale to look for string bundles if none are defined for your locale.  Translations for all strings
 * should be provided in this locale.
 */
//TODO: this really belongs in translation metadata, not in code
dojo.fallback_locale = 'en';

/**
 * Returns canonical form of locale, as used by Dojo.  All variants are case-insensitive and are separated by '-'
 * as specified in RFC 3066
 */
dojo.normalizeLocale = function(locale) {
    return locale ? locale.toLowerCase() : dojo.locale;
};

/**
 * requireLocalization() is for loading translated bundles provided within a package in the namespace.
 * Contents are typically strings, but may be any name/value pair, represented in JSON format.
 * A bundle is structured in a program as follows:
 *
 * <package>/
 *  nls/
 *   de/
 *    mybundle.js
 *   de-at/
 *    mybundle.js
 *   en/
 *    mybundle.js
 *   en-us/
 *    mybundle.js
 *   en-gb/
 *    mybundle.js
 *   es/
 *    mybundle.js
 *  ...etc
 *
 * where package is part of the namespace as used by dojo.require().  Each directory is named for a
 * locale as specified by RFC 3066, (http://www.ietf.org/rfc/rfc3066.txt), normalized in lowercase.
 *
 * For a given locale, string bundles will be loaded for that locale and all general locales above it, as well
 * as a system-specified fallback.  For example, "de_at" will also load "de" and "en".  Lookups will traverse
 * the locales in this order.  A build step can preload the bundles to avoid data redundancy and extra network hits.
 *
 * @param modulename package in which the bundle is found
 * @param bundlename bundle name, typically the filename without the '.js' suffix
 * @param locale the locale to load (optional)  By default, the browser's user locale as defined
 *    in dojo.locale
 */
dojo.requireLocalization = function(modulename, bundlename, locale /*optional*/){

    dojo.debug("EXPERIMENTAL: dojo.requireLocalization"); //dojo.experimental

    var syms = dojo.hostenv.getModuleSymbols(modulename);
    var modpath = syms.concat("nls").join("/");

    locale = dojo.normalizeLocale(locale);

    var elements = locale.split('-');
    var searchlist = [];
    for(var i = elements.length; i > 0; i--){
        searchlist.push(elements.slice(0, i).join('-'));
    }
    if(searchlist[searchlist.length-1] != dojo.fallback_locale){
        searchlist.push(dojo.fallback_locale);
    }

    var bundlepackage = [modulename, "_nls", bundlename].join(".");
    var bundle = dojo.hostenv.startPackage(bundlepackage);
    dojo.hostenv.loaded_modules_[bundlepackage] = bundle;
    
    var inherit = false;
    for(var i = searchlist.length - 1; i >= 0; i--){
        var loc = searchlist[i];
        var pkg = [bundlepackage, loc].join(".");
        var loaded = false;
        if(!dojo.hostenv.findModule(pkg)){
            // Mark loaded whether it's found or not, so that further load attempts will not be made
            dojo.hostenv.loaded_modules_[pkg] = null;

            var filespec = [modpath, loc, bundlename].join("/") + '.js';
            loaded = dojo.hostenv.loadPath(filespec, null, function(hash) {
                 bundle[loc] = hash;
                 if(inherit){
                    // Use mixins approach to copy string references from inherit bundle, but skip overrides.
                    for(var prop in inherit){
                        if(!bundle[loc][prop]){
                            bundle[loc][prop] = inherit[prop];
                        }
                    }
                 }
/*
                // Use prototype to point to other bundle, then copy in result from loadPath
                bundle[loc] = new function(){};
                if(inherit){ bundle[loc].prototype = inherit; }
                for(var i in hash){ bundle[loc][i] = hash[i]; }
*/
            });
        }else{
            loaded = true;
        }
        if(loaded && bundle[loc]){
            inherit = bundle[loc];
        }
    }
};

(function(){
    var extra = djConfig.extraLocale;
    if (extra) {
        var req = dojo.requireLocalization;
        dojo.requireLocalization = function(m, b, locale){
            req(m,b,locale);
            if (locale) return;
            if (djConfig.extraLocale instanceof Array){
                for (var i=0; i<extra.length; i++){
                    req(m,b,extra[i]);
                }
            }else{
                req(m,b,extra);
            }
        }
    }
})();

dojo.provide("dojo.string.common");


/**
 * Trim whitespace from 'str'. If 'wh' > 0,
 * only trim from start, if 'wh' < 0, only trim
 * from end, otherwise trim both ends
 */
dojo.string.trim = function(str, wh){
    if(!str.replace){ return str; }
    if(!str.length){ return str; }
    var re = (wh > 0) ? (/^\s+/) : (wh < 0) ? (/\s+$/) : (/^\s+|\s+$/g);
    return str.replace(re, "");
}

/**
 * Trim whitespace at the beginning of 'str'
 */
dojo.string.trimStart = function(str) {
    return dojo.string.trim(str, 1);
}

/**
 * Trim whitespace at the end of 'str'
 */
dojo.string.trimEnd = function(str) {
    return dojo.string.trim(str, -1);
}

/**
 * Return 'str' repeated 'count' times, optionally
 * placing 'separator' between each rep
 */
dojo.string.repeat = function(str, count, separator) {
    var out = "";
    for(var i = 0; i < count; i++) {
        out += str;
        if(separator && i < count - 1) {
            out += separator;
        }
    }
    return out;
}

/**
 * Pad 'str' to guarantee that it is at least 'len' length
 * with the character 'c' at either the start (dir=1) or
 * end (dir=-1) of the string
 */
dojo.string.pad = function(str, len/*=2*/, c/*='0'*/, dir/*=1*/) {
    var out = String(str);
    if(!c) {
        c = '0';
    }
    if(!dir) {
        dir = 1;
    }
    while(out.length < len) {
        if(dir > 0) {
            out = c + out;
        } else {
            out += c;
        }
    }
    return out;
}

/** same as dojo.string.pad(str, len, c, 1) */
dojo.string.padLeft = function(str, len, c) {
    return dojo.string.pad(str, len, c, 1);
}

/** same as dojo.string.pad(str, len, c, -1) */
dojo.string.padRight = function(str, len, c) {
    return dojo.string.pad(str, len, c, -1);
}

dojo.provide("dojo.string");

dojo.provide("dojo.lang.common");


dojo.lang._mixin = function(/*Object*/ obj, /*Object*/ props){
    // summary:    Adds all properties and methods of props to obj.
    var tobj = {};
    for(var x in props){
        // the "tobj" condition avoid copying properties in "props"
        // inherited from Object.prototype.  For example, if obj has a custom
        // toString() method, don't overwrite it with the toString() method
        // that props inherited from Object.protoype
        if(typeof tobj[x] == "undefined" || tobj[x] != props[x]) {
            obj[x] = props[x];
        }
    }
    // IE doesn't recognize custom toStrings in for..in
    if(dojo.render.html.ie && dojo.lang.isFunction(props["toString"]) && props["toString"] != obj["toString"]) {
        obj.toString = props.toString;
    }
    return obj;
}

dojo.lang.mixin = function(/*Object*/ obj, /*Object...*/ props){
    // summary:    Adds all properties and methods of props to obj.
    for(var i=1, l=arguments.length; i<l; i++){
        dojo.lang._mixin(obj, arguments[i]);
    }
    return obj; // Object
}

dojo.lang.extend = function(/*Object*/ constructor, /*Object...*/ props){
    // summary:    Adds all properties and methods of props to constructors prototype,
    //            making them available to all instances created with constructor.
    for(var i=1, l=arguments.length; i<l; i++){
        dojo.lang._mixin(constructor.prototype, arguments[i]);
    }
    return constructor;
}

dojo.lang.find = function(    /*Array*/        array, 
                            /*Object*/        value,
                            /*Boolean?*/    identity,
                            /*Boolean?*/    findLast){
    // summary:    Return the index of value in array, returning -1 if not found.
    
    // param: identity:  If true, matches with identity comparison (===).  
    //                     If false, uses normal comparison (==).
    // param: findLast:  If true, returns index of last instance of value.
    // usage:
    //  find(array, value[, identity [findLast]]) // recommended
    // usage:
     //  find(value, array[, identity [findLast]]) // deprecated
                            
    // support both (array, value) and (value, array)
    if(!dojo.lang.isArrayLike(array) && dojo.lang.isArrayLike(value)) {
        dojo.deprecated('dojo.lang.find(value, array)', 'use dojo.lang.find(array, value) instead', "0.5");
        var temp = array;
        array = value;
        value = temp;
    }
    var isString = dojo.lang.isString(array);
    if(isString) { array = array.split(""); }

    if(findLast) {
        var step = -1;
        var i = array.length - 1;
        var end = -1;
    } else {
        var step = 1;
        var i = 0;
        var end = array.length;
    }
    if(identity){
        while(i != end) {
            if(array[i] === value){ return i; }
            i += step;
        }
    }else{
        while(i != end) {
            if(array[i] == value){ return i; }
            i += step;
        }
    }
    return -1;    // number
}

dojo.lang.indexOf = dojo.lang.find;

dojo.lang.findLast = function(/*Array*/ array, /*Object*/ value, /*boolean?*/ identity){
    // summary:    Return index of last occurance of value in array, returning -1 if not found.

    // param: identity:  If true, matches with identity comparison (===).  
    //                     If false, uses normal comparison (==).
    return dojo.lang.find(array, value, identity, true);
}

dojo.lang.lastIndexOf = dojo.lang.findLast;

dojo.lang.inArray = function(array /*Array*/, value /*Object*/){
    // summary:    Return true if value is present in array.
    return dojo.lang.find(array, value) > -1; // return: boolean
}

/**
 * Partial implmentation of is* functions from
 * http://www.crockford.com/javascript/recommend.html
 * NOTE: some of these may not be the best thing to use in all situations
 * as they aren't part of core JS and therefore can't work in every case.
 * See WARNING messages inline for tips.
 *
 * The following is* functions are fairly "safe"
 */

dojo.lang.isObject = function(it){
    // summary:    Return true if it is an Object, Array or Function.
    if(typeof it == "undefined"){ return false; }
    return (typeof it == "object" || it === null || dojo.lang.isArray(it) || dojo.lang.isFunction(it));
}

dojo.lang.isArray = function(it){
    // summary:    Return true if it is an Array.
    return (it instanceof Array || typeof it == "array");
}

dojo.lang.isArrayLike = function(it){
    // summary:    Return true if it can be used as an array (i.e. is an object with an integer length property).
    if(dojo.lang.isString(it)){ return false; }
    if(dojo.lang.isFunction(it)){ return false; } // keeps out built-in constructors (Number, String, ...) which have length properties
    if(dojo.lang.isArray(it)){ return true; }
    if(typeof it != "undefined" && it
        && dojo.lang.isNumber(it.length) && isFinite(it.length)){ return true; }
    return false;
}

dojo.lang.isFunction = function(it){
    // summary:    Return true if it is a Function.
    if(!it){ return false; }
    return (it instanceof Function || typeof it == "function");
}

dojo.lang.isString = function(it){
    // summary:    Return true if it is a String.
    return (it instanceof String || typeof it == "string");
}

dojo.lang.isAlien = function(it){
    // summary:    Return true if it is not a built-in function.
    if(!it){ return false; }
    return !dojo.lang.isFunction() && /\{\s*\[native code\]\s*\}/.test(String(it));
}

dojo.lang.isBoolean = function(it){
    // summary:    Return true if it is a Boolean.
    return (it instanceof Boolean || typeof it == "boolean");
}

/**
 * The following is***() functions are somewhat "unsafe". Fortunately,
 * there are workarounds the the language provides and are mentioned
 * in the WARNING messages.
 *
 */
dojo.lang.isNumber = function(it){
    // summary:    Return true if it is a number.

    // warning: 
    //        In most cases, isNaN(it) is sufficient to determine whether or not
    //         something is a number or can be used as such. For example, a number or string
    //         can be used interchangably when accessing array items (array["1"] is the same as
    //         array[1]) and isNaN will return false for both values ("1" and 1). However,
    //         isNumber("1")  will return false, which is generally not too useful.
    //         Also, isNumber(NaN) returns true, again, this isn't generally useful, but there
    //         are corner cases (like when you want to make sure that two things are really
    //         the same type of thing). That is really where isNumber "shines".
    //
    // recommendation: Use isNaN(it) when possible
    
    return (it instanceof Number || typeof it == "number");
}

/*
 * FIXME: Should isUndefined go away since it is error prone?
 */
dojo.lang.isUndefined = function(it){
    // summary: Return true if it is not defined.
    
    // warning: In some cases, isUndefined will not behave as you
    //         might expect. If you do isUndefined(foo) and there is no earlier
    //         reference to foo, an error will be thrown before isUndefined is
    //         called. It behaves correctly if you scope yor object first, i.e.
    //         isUndefined(foo.bar) where foo is an object and bar isn't a
    //         property of the object.
    //
    // recommendation: Use typeof foo == "undefined" when possible

    return ((it == undefined)&&(typeof it == "undefined"));
}

// end Crockford functions

dojo.provide("dojo.lang.extras");


/**
 * Sets a timeout in milliseconds to execute a function in a given context
 * with optional arguments.
 *
 * setTimeout (Object context, function func, number delay[, arg1[, ...]]);
 * setTimeout (function func, number delay[, arg1[, ...]]);
 */
dojo.lang.setTimeout = function(func, delay){
    var context = window, argsStart = 2;
    if(!dojo.lang.isFunction(func)){
        context = func;
        func = delay;
        delay = arguments[2];
        argsStart++;
    }

    if(dojo.lang.isString(func)){
        func = context[func];
    }
    
    var args = [];
    for (var i = argsStart; i < arguments.length; i++) {
        args.push(arguments[i]);
    }
    return dojo.global().setTimeout(function () { func.apply(context, args); }, delay);
}

dojo.lang.clearTimeout = function(timer){
    dojo.global().clearTimeout(timer);
}

dojo.lang.getNameInObj = function(ns, item){
    if(!ns){ ns = dj_global; }

    for(var x in ns){
        if(ns[x] === item){
            return new String(x);
        }
    }
    return null;
}

dojo.lang.shallowCopy = function(obj) {
    var ret = {}, key;
    for(key in obj) {
        if(dojo.lang.isUndefined(ret[key])) {
            ret[key] = obj[key];
        }
    }
    return ret;
}

/**
 * Return the first argument that isn't undefined
 */
dojo.lang.firstValued = function(/* ... */) {
    for(var i = 0; i < arguments.length; i++) {
        if(typeof arguments[i] != "undefined") {
            return arguments[i];
        }
    }
    return undefined;
}

/**
 * Get a value from a reference specified as a string descriptor,
 * (e.g. "A.B") in the given context.
 * 
 * getObjPathValue(String objpath [, Object context, Boolean create])
 *
 * If context is not specified, dj_global is used
 * If create is true, undefined objects in the path are created.
 */
dojo.lang.getObjPathValue = function(objpath, context, create){
    with(dojo.parseObjPath(objpath, context, create)){
        return dojo.evalProp(prop, obj, create);
    }
}

/**
 * Set a value on a reference specified as a string descriptor. 
 * (e.g. "A.B") in the given context.
 * 
 * setObjPathValue(String objpath, value [, Object context, Boolean create])
 *
 * If context is not specified, dj_global is used
 * If create is true, undefined objects in the path are created.
 */
dojo.lang.setObjPathValue = function(objpath, value, context, create){
    if(arguments.length < 4){
        create = true;
    }
    with(dojo.parseObjPath(objpath, context, create)){
        if(obj && (create || (prop in obj))){
            obj[prop] = value;
        }
    }
}

dojo.provide("dojo.io.IO");

/******************************************************************************
 *    Notes about dojo.io design:
 *    
 *    The dojo.io.* package has the unenviable task of making a lot of different
 *    types of I/O feel natural, despite a universal lack of good (or even
 *    reasonable!) I/O capability in the host environment. So lets pin this down
 *    a little bit further.
 *
 *    Rhino:
 *        perhaps the best situation anywhere. Access to Java classes allows you
 *        to do anything one might want in terms of I/O, both synchronously and
 *        async. Can open TCP sockets and perform low-latency client/server
 *        interactions. HTTP transport is available through Java HTTP client and
 *        server classes. Wish it were always this easy.
 *
 *    xpcshell:
 *        XPCOM for I/O. A cluster-fuck to be sure.
 *
 *    spidermonkey:
 *        S.O.L.
 *
 *    Browsers:
 *        Browsers generally do not provide any useable filesystem access. We are
 *        therefore limited to HTTP for moving information to and from Dojo
 *        instances living in a browser.
 *
 *        XMLHTTP:
 *            Sync or async, allows reading of arbitrary text files (including
 *            JS, which can then be eval()'d), writing requires server
 *            cooperation and is limited to HTTP mechanisms (POST and GET).
 *
 *        <iframe> hacks:
 *            iframe document hacks allow browsers to communicate asynchronously
 *            with a server via HTTP POST and GET operations. With significant
 *            effort and server cooperation, low-latency data transit between
 *            client and server can be acheived via iframe mechanisms (repubsub).
 *
 *        SVG:
 *            Adobe's SVG viewer implements helpful primitives for XML-based
 *            requests, but receipt of arbitrary text data seems unlikely w/o
 *            <![CDATA[]]> sections.
 *
 *
 *    A discussion between Dylan, Mark, Tom, and Alex helped to lay down a lot
 *    the IO API interface. A transcript of it can be found at:
 *        http://dojotoolkit.org/viewcvs/viewcvs.py/documents/irc/irc_io_api_log.txt?rev=307&view=auto
 *    
 *    Also referenced in the design of the API was the DOM 3 L&S spec:
 *        http://www.w3.org/TR/2004/REC-DOM-Level-3-LS-20040407/load-save.html
 ******************************************************************************/

// a map of the available transport options. Transports should add themselves
// by calling add(name)
dojo.io.transports = [];
dojo.io.hdlrFuncNames = [ "load", "error", "timeout" ]; // we're omitting a progress() event for now

dojo.io.Request = function(url, mimetype, transport, changeUrl){
    if((arguments.length == 1)&&(arguments[0].constructor == Object)){
        this.fromKwArgs(arguments[0]);
    }else{
        this.url = url;
        if(mimetype){ this.mimetype = mimetype; }
        if(transport){ this.transport = transport; }
        if(arguments.length >= 4){ this.changeUrl = changeUrl; }
    }
}

dojo.lang.extend(dojo.io.Request, {

    /** The URL to hit */
    url: "",
    
    /** The mime type used to interrpret the response body */
    mimetype: "text/plain",
    
    /** The HTTP method to use */
    method: "GET",
    
    /** An Object containing key-value pairs to be included with the request */
    content: undefined, // Object
    
    /** The transport medium to use */
    transport: undefined, // String
    
    /** If defined the URL of the page is physically changed */
    changeUrl: undefined, // String
    
    /** A form node to use in the request */
    formNode: undefined, // HTMLFormElement
    
    /** Whether the request should be made synchronously */
    sync: false,
    
    bindSuccess: false,

    /** Cache/look for the request in the cache before attempting to request?
     *  NOTE: this isn't a browser cache, this is internal and would only cache in-page
     */
    useCache: false,

    /** Prevent the browser from caching this by adding a query string argument to the URL */
    preventCache: false,
    
    // events stuff
    load: function(type, data, evt){ },
    error: function(type, error){ },
    timeout: function(type){ },
    handle: function(){ },

    //FIXME: change BrowserIO.js to use timeouts? IframeIO?
    // The number of seconds to wait until firing a timeout callback.
    // If it is zero, that means, don't do a timeout check.
    timeoutSeconds: 0,
    
    // the abort method needs to be filled in by the transport that accepts the
    // bind() request
    abort: function(){ },
    
    // backButton: function(){ },
    // forwardButton: function(){ },

    fromKwArgs: function(kwArgs){
        // normalize args
        if(kwArgs["url"]){ kwArgs.url = kwArgs.url.toString(); }
        if(kwArgs["formNode"]) { kwArgs.formNode = dojo.byId(kwArgs.formNode); }
        if(!kwArgs["method"] && kwArgs["formNode"] && kwArgs["formNode"].method) {
            kwArgs.method = kwArgs["formNode"].method;
        }
        
        // backwards compatibility
        if(!kwArgs["handle"] && kwArgs["handler"]){ kwArgs.handle = kwArgs.handler; }
        if(!kwArgs["load"] && kwArgs["loaded"]){ kwArgs.load = kwArgs.loaded; }
        if(!kwArgs["changeUrl"] && kwArgs["changeURL"]) { kwArgs.changeUrl = kwArgs.changeURL; }

        // encoding fun!
        kwArgs.encoding = dojo.lang.firstValued(kwArgs["encoding"], djConfig["bindEncoding"], "");

        kwArgs.sendTransport = dojo.lang.firstValued(kwArgs["sendTransport"], djConfig["ioSendTransport"], false);

        var isFunction = dojo.lang.isFunction;
        for(var x=0; x<dojo.io.hdlrFuncNames.length; x++){
            var fn = dojo.io.hdlrFuncNames[x];
            if(kwArgs[fn] && isFunction(kwArgs[fn])){ continue; }
            if(kwArgs["handle"] && isFunction(kwArgs["handle"])){
                kwArgs[fn] = kwArgs.handle;
            }
            // handler is aliased above, shouldn't need this check
            /* else if(dojo.lang.isObject(kwArgs.handler)){
                if(isFunction(kwArgs.handler[fn])){
                    kwArgs[fn] = kwArgs.handler[fn]||kwArgs.handler["handle"]||function(){};
                }
            }*/
        }
        dojo.lang.mixin(this, kwArgs);
    }

});

dojo.io.Error = function(msg, type, num){
    this.message = msg;
    this.type =  type || "unknown"; // must be one of "io", "parse", "unknown"
    this.number = num || 0; // per-substrate error number, not normalized
}

dojo.io.transports.addTransport = function(name){
    this.push(name);
    // FIXME: do we need to handle things that aren't direct children of the
    // dojo.io namespace? (say, dojo.io.foo.fooTransport?)
    this[name] = dojo.io[name];
}

// binding interface, the various implementations register their capabilities
// and the bind() method dispatches
dojo.io.bind = function(request){
    // if the request asks for a particular implementation, use it
    if(!(request instanceof dojo.io.Request)){
        try{
            request = new dojo.io.Request(request);
        }catch(e){ dojo.debug(e); }
    }
    var tsName = "";
    if(request["transport"]){
        tsName = request["transport"];
        // FIXME: it would be good to call the error handler, although we'd
        // need to use setTimeout or similar to accomplish this and we can't
        // garuntee that this facility is available.
        if(!this[tsName]){ return request; }
    }else{
        // otherwise we do our best to auto-detect what available transports
        // will handle 
        for(var x=0; x<dojo.io.transports.length; x++){
            var tmp = dojo.io.transports[x];
            if((this[tmp])&&(this[tmp].canHandle(request))){
                tsName = tmp;
            }
        }
        if(tsName == ""){ return request; }
    }
    this[tsName].bind(request);
    request.bindSuccess = true;
    return request;
}

dojo.io.queueBind = function(request){
    if(!(request instanceof dojo.io.Request)){
        try{
            request = new dojo.io.Request(request);
        }catch(e){ dojo.debug(e); }
    }

    // make sure we get called if/when we get a response
    var oldLoad = request.load;
    request.load = function(){
        dojo.io._queueBindInFlight = false;
        var ret = oldLoad.apply(this, arguments);
        dojo.io._dispatchNextQueueBind();
        return ret;
    }

    var oldErr = request.error;
    request.error = function(){
        dojo.io._queueBindInFlight = false;
        var ret = oldErr.apply(this, arguments);
        dojo.io._dispatchNextQueueBind();
        return ret;
    }

    dojo.io._bindQueue.push(request);
    dojo.io._dispatchNextQueueBind();
    return request;
}

dojo.io._dispatchNextQueueBind = function(){
    if(!dojo.io._queueBindInFlight){
        dojo.io._queueBindInFlight = true;
        if(dojo.io._bindQueue.length > 0){
            dojo.io.bind(dojo.io._bindQueue.shift());
        }else{
            dojo.io._queueBindInFlight = false;
        }
    }
}
dojo.io._bindQueue = [];
dojo.io._queueBindInFlight = false;

dojo.io.argsFromMap = function(map, encoding, last){
    var enc = /utf/i.test(encoding||"") ? encodeURIComponent : dojo.string.encodeAscii;
    var mapped = [];
    var control = new Object();
    for(var name in map){
        var domap = function(elt){
            var val = enc(name)+"="+enc(elt);
            mapped[(last == name) ? "push" : "unshift"](val);
        }
        if(!control[name]){
            var value = map[name];
            // FIXME: should be isArrayLike?
            if (dojo.lang.isArray(value)){
                dojo.lang.forEach(value, domap);
            }else{
                domap(value);
            }
        }
    }
    return mapped.join("&");
}

dojo.io.setIFrameSrc = function(iframe, src, replace){
    try{
        var r = dojo.render.html;
        // dojo.debug(iframe);
        if(!replace){
            if(r.safari){
                iframe.location = src;
            }else{
                frames[iframe.name].location = src;
            }
        }else{
            // Fun with DOM 0 incompatibilities!
            var idoc;
            if(r.ie){
                idoc = iframe.contentWindow.document;
            }else if(r.safari){
                idoc = iframe.document;
            }else{ //  if(r.moz){
                idoc = iframe.contentWindow;
            }

            //For Safari (at least 2.0.3) and Opera, if the iframe
            //has just been created but it doesn't have content
            //yet, then iframe.document may be null. In that case,
            //use iframe.location and return.
            if(!idoc){
                iframe.location = src;
                return;
            }else{
                idoc.location.replace(src);
            }
        }
    }catch(e){ 
        dojo.debug(e); 
        dojo.debug("setIFrameSrc: "+e); 
    }
}

/*
dojo.io.sampleTranport = new function(){
    this.canHandle = function(kwArgs){
        // canHandle just tells dojo.io.bind() if this is a good transport to
        // use for the particular type of request.
        if(    
            (
                (kwArgs["mimetype"] == "text/plain") ||
                (kwArgs["mimetype"] == "text/html") ||
                (kwArgs["mimetype"] == "text/javascript")
            )&&(
                (kwArgs["method"] == "get") ||
                ( (kwArgs["method"] == "post") && (!kwArgs["formNode"]) )
            )
        ){
            return true;
        }

        return false;
    }

    this.bind = function(kwArgs){
        var hdlrObj = {};

        // set up a handler object
        for(var x=0; x<dojo.io.hdlrFuncNames.length; x++){
            var fn = dojo.io.hdlrFuncNames[x];
            if(typeof kwArgs.handler == "object"){
                if(typeof kwArgs.handler[fn] == "function"){
                    hdlrObj[fn] = kwArgs.handler[fn]||kwArgs.handler["handle"];
                }
            }else if(typeof kwArgs[fn] == "function"){
                hdlrObj[fn] = kwArgs[fn];
            }else{
                hdlrObj[fn] = kwArgs["handle"]||function(){};
            }
        }

        // build a handler function that calls back to the handler obj
        var hdlrFunc = function(evt){
            if(evt.type == "onload"){
                hdlrObj.load("load", evt.data, evt);
            }else if(evt.type == "onerr"){
                var errObj = new dojo.io.Error("sampleTransport Error: "+evt.msg);
                hdlrObj.error("error", errObj);
            }
        }

        // the sample transport would attach the hdlrFunc() when sending the
        // request down the pipe at this point
        var tgtURL = kwArgs.url+"?"+dojo.io.argsFromMap(kwArgs.content);
        // sampleTransport.sendRequest(tgtURL, hdlrFunc);
    }

    dojo.io.transports.addTransport("sampleTranport");
}
*/

dojo.provide("dojo.lang.array");


// FIXME: Is this worthless since you can do: if(name in obj)
// is this the right place for this?
dojo.lang.has = function(obj, name){
    try{
        return (typeof obj[name] != "undefined");
    }catch(e){ return false; }
}

dojo.lang.isEmpty = function(obj) {
    if(dojo.lang.isObject(obj)) {
        var tmp = {};
        var count = 0;
        for(var x in obj){
            if(obj[x] && (!tmp[x])){
                count++;
                break;
            } 
        }
        return (count == 0);
    } else if(dojo.lang.isArrayLike(obj) || dojo.lang.isString(obj)) {
        return obj.length == 0;
    }
}

dojo.lang.map = function(arr, obj, unary_func){
    var isString = dojo.lang.isString(arr);
    if(isString){
        arr = arr.split("");
    }
    if(dojo.lang.isFunction(obj)&&(!unary_func)){
        unary_func = obj;
        obj = dj_global;
    }else if(dojo.lang.isFunction(obj) && unary_func){
        // ff 1.5 compat
        var tmpObj = obj;
        obj = unary_func;
        unary_func = tmpObj;
    }
    if(Array.map){
         var outArr = Array.map(arr, unary_func, obj);
    }else{
        var outArr = [];
        for(var i=0;i<arr.length;++i){
            outArr.push(unary_func.call(obj, arr[i]));
        }
    }
    if(isString) {
        return outArr.join("");
    } else {
        return outArr;
    }
}

// http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:forEach
dojo.lang.forEach = function(anArray /* Array */, callback /* Function */, thisObject /* Object */){
    if(dojo.lang.isString(anArray)){ 
        anArray = anArray.split(""); 
    }
    if(Array.forEach){
        Array.forEach(anArray, callback, thisObject);
    }else{
        // FIXME: there are several ways of handilng thisObject. Is dj_global always the default context?
        if(!thisObject){
            thisObject=dj_global;
        }
        for(var i=0,l=anArray.length; i<l; i++){ 
            callback.call(thisObject, anArray[i], i, anArray);
        }
    }
}

dojo.lang._everyOrSome = function(every, arr, callback, thisObject){
    if(dojo.lang.isString(arr)){ 
        arr = arr.split(""); 
    }
    if(Array.every){
        return Array[ (every) ? "every" : "some" ](arr, callback, thisObject);
    }else{
        if(!thisObject){
            thisObject = dj_global;
        }
        for(var i=0,l=arr.length; i<l; i++){
            var result = callback.call(thisObject, arr[i], i, arr);
            if((every)&&(!result)){
                return false;
            }else if((!every)&&(result)){
                return true;
            }
        }
        return (every) ? true : false;
    }
}

dojo.lang.every = function(arr, callback, thisObject){
    return this._everyOrSome(true, arr, callback, thisObject);
}

dojo.lang.some = function(arr, callback, thisObject){
    return this._everyOrSome(false, arr, callback, thisObject);
}

dojo.lang.filter = function(arr, callback, thisObject) {
    var isString = dojo.lang.isString(arr);
    if(isString) { arr = arr.split(""); }
    if(Array.filter) {
        var outArr = Array.filter(arr, callback, thisObject);
    } else {
        if(!thisObject) {
            if(arguments.length >= 3) { dojo.raise("thisObject doesn't exist!"); }
            thisObject = dj_global;
        }

        var outArr = [];
        for(var i = 0; i < arr.length; i++) {
            if(callback.call(thisObject, arr[i], i, arr)) {
                outArr.push(arr[i]);
            }
        }
    }
    if(isString) {
        return outArr.join("");
    } else {
        return outArr;
    }
}

/**
 * Creates a 1-D array out of all the arguments passed,
 * unravelling any array-like objects in the process
 *
 * Ex:
 * unnest(1, 2, 3) ==> [1, 2, 3]
 * unnest(1, [2, [3], [[[4]]]]) ==> [1, 2, 3, 4]
 */
dojo.lang.unnest = function(/* ... */) {
    var out = [];
    for(var i = 0; i < arguments.length; i++) {
        if(dojo.lang.isArrayLike(arguments[i])) {
            var add = dojo.lang.unnest.apply(this, arguments[i]);
            out = out.concat(add);
        } else {
            out.push(arguments[i]);
        }
    }
    return out;
}

/**
 * Converts an array-like object (i.e. arguments, DOMCollection)
 * to an array
**/
dojo.lang.toArray = function(arrayLike, startOffset) {
    var array = [];
    for(var i = startOffset||0; i < arrayLike.length; i++) {
        array.push(arrayLike[i]);
    }
    return array;
}

dojo.provide("dojo.lang.func");


/**
 * Runs a function in a given scope (thisObject), can
 * also be used to preserve scope.
 *
 * hitch(foo, "bar"); // runs foo.bar() in the scope of foo
 * hitch(foo, myFunction); // runs myFunction in the scope of foo
 */
dojo.lang.hitch = function(thisObject, method) {
    var fcn = dojo.lang.isString(method) ? thisObject[method] : method;

    return function() {
        return fcn.apply(thisObject, arguments);
    };
}

dojo.lang.anonCtr = 0;
dojo.lang.anon = {};
dojo.lang.nameAnonFunc = function(anonFuncPtr, namespaceObj, searchForNames){
    var nso = (namespaceObj || dojo.lang.anon);
    if( (searchForNames) ||
        ((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"] == true)) ){
        for(var x in nso){
            try{
                if(nso[x] === anonFuncPtr){
                    return x;
                }
            }catch(e){} // window.external fails in IE embedded in Eclipse (Eclipse bug #151165)
        }
    }
    var ret = "__"+dojo.lang.anonCtr++;
    while(typeof nso[ret] != "undefined"){
        ret = "__"+dojo.lang.anonCtr++;
    }
    nso[ret] = anonFuncPtr;
    return ret;
}

dojo.lang.forward = function(funcName){
    // Returns a function that forwards a method call to this.func(...)
    return function(){
        return this[funcName].apply(this, arguments);
    };
}

dojo.lang.curry = function(ns, func /* args ... */){
    var outerArgs = [];
    ns = ns||dj_global;
    if(dojo.lang.isString(func)){
        func = ns[func];
    }
    for(var x=2; x<arguments.length; x++){
        outerArgs.push(arguments[x]);
    }
    // since the event system replaces the original function with a new
    // join-point runner with an arity of 0, we check to see if it's left us
    // any clues about the original arity in lieu of the function's actual
    // length property
    var ecount = (func["__preJoinArity"]||func.length) - outerArgs.length;
    // borrowed from svend tofte
    function gather(nextArgs, innerArgs, expected){
        var texpected = expected;
        var totalArgs = innerArgs.slice(0); // copy
        for(var x=0; x<nextArgs.length; x++){
            totalArgs.push(nextArgs[x]);
        }
        // check the list of provided nextArgs to see if it, plus the
        // number of innerArgs already supplied, meets the total
        // expected.
        expected = expected-nextArgs.length;
        if(expected<=0){
            var res = func.apply(ns, totalArgs);
            expected = texpected;
            return res;
        }else{
            return function(){
                return gather(arguments,// check to see if we've been run
                                        // with enough args
                            totalArgs,    // a copy
                            expected);    // how many more do we need to run?;
            };
        }
    }
    return gather([], outerArgs, ecount);
}

dojo.lang.curryArguments = function(ns, func, args, offset){
    var targs = [];
    var x = offset||0;
    for(x=offset; x<args.length; x++){
        targs.push(args[x]); // ensure that it's an arr
    }
    return dojo.lang.curry.apply(dojo.lang, [ns, func].concat(targs));
}

dojo.lang.tryThese = function(){
    for(var x=0; x<arguments.length; x++){
        try{
            if(typeof arguments[x] == "function"){
                var ret = (arguments[x]());
                if(ret){
                    return ret;
                }
            }
        }catch(e){
            dojo.debug(e);
        }
    }
}

dojo.lang.delayThese = function(farr, cb, delay, onend){
    /**
     * alternate: (array funcArray, function callback, function onend)
     * alternate: (array funcArray, function callback)
     * alternate: (array funcArray)
     */
    if(!farr.length){ 
        if(typeof onend == "function"){
            onend();
        }
        return;
    }
    if((typeof delay == "undefined")&&(typeof cb == "number")){
        delay = cb;
        cb = function(){};
    }else if(!cb){
        cb = function(){};
        if(!delay){ delay = 0; }
    }
    setTimeout(function(){
        (farr.shift())();
        cb();
        dojo.lang.delayThese(farr, cb, delay, onend);
    }, delay);
}

dojo.provide("dojo.string.extras");


/**
 * Performs parameterized substitutions on a string.  For example,
 *   dojo.string.substituteParams("File '%{0}' is not found in directory '%{1}'.","foo.html","/temp");
 * returns
 *   "File 'foo.html' is not found in directory '/temp'."
 * 
 * @param template the original string template with %{values} to be replaced
 * @param hash name/value pairs (type object) to provide substitutions.  Alternatively, substitutions may be
 *  included as arguments 1..n to this function, corresponding to template parameters 0..n-1
 * @return the completed string. Throws an exception if any parameter is unmatched
 */
//TODO: use ${} substitution syntax instead, like widgets do?
dojo.string.substituteParams = function(template /*string */, hash /* object - optional or ... */) {
    var map = (typeof hash == 'object') ? hash : dojo.lang.toArray(arguments, 1);

    return template.replace(/\%\{(\w+)\}/g, function(match, key){
        return map[key] || dojo.raise("Substitution not found: " + key);
    });
};

/** Uppercases the first letter of each word */
dojo.string.capitalize = function (str) {
    if (!dojo.lang.isString(str)) { return ""; }
    if (arguments.length == 0) { str = this; }

    var words = str.split(' ');
    for(var i=0; i<words.length; i++){
        words[i] = words[i].charAt(0).toUpperCase() + words[i].substring(1);
    }
    return words.join(" ");
}

/**
 * Return true if the entire string is whitespace characters
 */
dojo.string.isBlank = function (str) {
    if(!dojo.lang.isString(str)) { return true; }
    return (dojo.string.trim(str).length == 0);
}

dojo.string.encodeAscii = function(str) {
    if(!dojo.lang.isString(str)) { return str; }
    var ret = "";
    var value = escape(str);
    var match, re = /%u([0-9A-F]{4})/i;
    while((match = value.match(re))) {
        var num = Number("0x"+match[1]);
        var newVal = escape("&#" + num + ";");
        ret += value.substring(0, match.index) + newVal;
        value = value.substring(match.index+match[0].length);
    }
    ret += value.replace(/\+/g, "%2B");
    return ret;
}

dojo.string.escape = function(type, str) {
    var args = dojo.lang.toArray(arguments, 1);
    switch(type.toLowerCase()) {
        case "xml":
        case "html":
        case "xhtml":
            return dojo.string.escapeXml.apply(this, args);
        case "sql":
            return dojo.string.escapeSql.apply(this, args);
        case "regexp":
        case "regex":
            return dojo.string.escapeRegExp.apply(this, args);
        case "javascript":
        case "jscript":
        case "js":
            return dojo.string.escapeJavaScript.apply(this, args);
        case "ascii":
            // so it's encode, but it seems useful
            return dojo.string.encodeAscii.apply(this, args);
        default:
            return str;
    }
}

dojo.string.escapeXml = function(str, noSingleQuotes) {
    str = str.replace(/&/gm, "&amp;").replace(/</gm, "&lt;")
        .replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
    if(!noSingleQuotes) { str = str.replace(/'/gm, "&#39;"); }
    return str;
}

dojo.string.escapeSql = function(str) {
    return str.replace(/'/gm, "''");
}

dojo.string.escapeRegExp = function(str) {
    return str.replace(/\\/gm, "\\\\").replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm, "\\$1");
}

dojo.string.escapeJavaScript = function(str) {
    return str.replace(/(["'\f\b\n\t\r])/gm, "\\$1");
}

dojo.string.escapeString = function(str){ 
    return ('"' + str.replace(/(["\\])/g, '\\$1') + '"'
        ).replace(/[\f]/g, "\\f"
        ).replace(/[\b]/g, "\\b"
        ).replace(/[\n]/g, "\\n"
        ).replace(/[\t]/g, "\\t"
        ).replace(/[\r]/g, "\\r");
}

// TODO: make an HTML version
dojo.string.summary = function(str, len) {
    if(!len || str.length <= len) {
        return str;
    } else {
        return str.substring(0, len).replace(/\.+$/, "") + "...";
    }
}

/**
 * Returns true if 'str' ends with 'end'
 */
dojo.string.endsWith = function(str, end, ignoreCase) {
    if(ignoreCase) {
        str = str.toLowerCase();
        end = end.toLowerCase();
    }
    if((str.length - end.length) < 0){
        return false;
    }
    return str.lastIndexOf(end) == str.length - end.length;
}

/**
 * Returns true if 'str' ends with any of the arguments[2 -> n]
 */
dojo.string.endsWithAny = function(str /* , ... */) {
    for(var i = 1; i < arguments.length; i++) {
        if(dojo.string.endsWith(str, arguments[i])) {
            return true;
        }
    }
    return false;
}

/**
 * Returns true if 'str' starts with 'start'
 */
dojo.string.startsWith = function(str, start, ignoreCase) {
    if(ignoreCase) {
        str = str.toLowerCase();
        start = start.toLowerCase();
    }
    return str.indexOf(start) == 0;
}

/**
 * Returns true if 'str' starts with any of the arguments[2 -> n]
 */
dojo.string.startsWithAny = function(str /* , ... */) {
    for(var i = 1; i < arguments.length; i++) {
        if(dojo.string.startsWith(str, arguments[i])) {
            return true;
        }
    }
    return false;
}

/**
 * Returns true if 'str' contains any of the arguments 2 -> n
 */
dojo.string.has = function(str /* , ... */) {
    for(var i = 1; i < arguments.length; i++) {
        if(str.indexOf(arguments[i]) > -1){
            return true;
        }
    }
    return false;
}

dojo.string.normalizeNewlines = function (text,newlineChar) {
    if (newlineChar == "\n") {
        text = text.replace(/\r\n/g, "\n");
        text = text.replace(/\r/g, "\n");
    } else if (newlineChar == "\r") {
        text = text.replace(/\r\n/g, "\r");
        text = text.replace(/\n/g, "\r");
    } else {
        text = text.replace(/([^\r])\n/g, "$1\r\n");
        text = text.replace(/\r([^\n])/g, "\r\n$1");
    }
    return text;
}

dojo.string.splitEscaped = function (str,charac) {
    var components = [];
    for (var i = 0, prevcomma = 0; i < str.length; i++) {
        if (str.charAt(i) == '\\') { i++; continue; }
        if (str.charAt(i) == charac) {
            components.push(str.substring(prevcomma, i));
            prevcomma = i + 1;
        }
    }
    components.push(str.substr(prevcomma));
    return components;
}

dojo.provide("dojo.dom");

dojo.dom.ELEMENT_NODE                  = 1;
dojo.dom.ATTRIBUTE_NODE                = 2;
dojo.dom.TEXT_NODE                     = 3;
dojo.dom.CDATA_SECTION_NODE            = 4;
dojo.dom.ENTITY_REFERENCE_NODE         = 5;
dojo.dom.ENTITY_NODE                   = 6;
dojo.dom.PROCESSING_INSTRUCTION_NODE   = 7;
dojo.dom.COMMENT_NODE                  = 8;
dojo.dom.DOCUMENT_NODE                 = 9;
dojo.dom.DOCUMENT_TYPE_NODE            = 10;
dojo.dom.DOCUMENT_FRAGMENT_NODE        = 11;
dojo.dom.NOTATION_NODE                 = 12;
    
dojo.dom.dojoml = "http://www.dojotoolkit.org/2004/dojoml";

/**
 *    comprehensive list of XML namespaces
**/
dojo.dom.xmlns = {
    svg : "http://www.w3.org/2000/svg",
    smil : "http://www.w3.org/2001/SMIL20/",
    mml : "http://www.w3.org/1998/Math/MathML",
    cml : "http://www.xml-cml.org",
    xlink : "http://www.w3.org/1999/xlink",
    xhtml : "http://www.w3.org/1999/xhtml",
    xul : "http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",
    xbl : "http://www.mozilla.org/xbl",
    fo : "http://www.w3.org/1999/XSL/Format",
    xsl : "http://www.w3.org/1999/XSL/Transform",
    xslt : "http://www.w3.org/1999/XSL/Transform",
    xi : "http://www.w3.org/2001/XInclude",
    xforms : "http://www.w3.org/2002/01/xforms",
    saxon : "http://icl.com/saxon",
    xalan : "http://xml.apache.org/xslt",
    xsd : "http://www.w3.org/2001/XMLSchema",
    dt: "http://www.w3.org/2001/XMLSchema-datatypes",
    xsi : "http://www.w3.org/2001/XMLSchema-instance",
    rdf : "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
    rdfs : "http://www.w3.org/2000/01/rdf-schema#",
    dc : "http://purl.org/dc/elements/1.1/",
    dcq: "http://purl.org/dc/qualifiers/1.0",
    "soap-env" : "http://schemas.xmlsoap.org/soap/envelope/",
    wsdl : "http://schemas.xmlsoap.org/wsdl/",
    AdobeExtensions : "http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
};

dojo.dom.isNode = function(wh){
    if(typeof Element == "object") {
        try {
            return wh instanceof Element;
        } catch(E) {}
    } else {
        // best-guess
        return wh && !isNaN(wh.nodeType);
    }
}

dojo.dom.getUniqueId = function(){
    var _document = dojo.doc();
    do {
        var id = "dj_unique_" + (++arguments.callee._idIncrement);
    }while(_document.getElementById(id));
    return id;
}
dojo.dom.getUniqueId._idIncrement = 0;

dojo.dom.firstElement = dojo.dom.getFirstChildElement = function(parentNode, tagName){
    var node = parentNode.firstChild;
    while(node && node.nodeType != dojo.dom.ELEMENT_NODE){
        node = node.nextSibling;
    }
    if(tagName && node && node.tagName && node.tagName.toLowerCase() != tagName.toLowerCase()) {
        node = dojo.dom.nextElement(node, tagName);
    }
    return node;
}

dojo.dom.lastElement = dojo.dom.getLastChildElement = function(parentNode, tagName){
    var node = parentNode.lastChild;
    while(node && node.nodeType != dojo.dom.ELEMENT_NODE) {
        node = node.previousSibling;
    }
    if(tagName && node && node.tagName && node.tagName.toLowerCase() != tagName.toLowerCase()) {
        node = dojo.dom.prevElement(node, tagName);
    }
    return node;
}

dojo.dom.nextElement = dojo.dom.getNextSiblingElement = function(node, tagName){
    if(!node) { return null; }
    do {
        node = node.nextSibling;
    } while(node && node.nodeType != dojo.dom.ELEMENT_NODE);

    if(node && tagName && tagName.toLowerCase() != node.tagName.toLowerCase()) {
        return dojo.dom.nextElement(node, tagName);
    }
    return node;
}

dojo.dom.prevElement = dojo.dom.getPreviousSiblingElement = function(node, tagName){
    if(!node) { return null; }
    if(tagName) { tagName = tagName.toLowerCase(); }
    do {
        node = node.previousSibling;
    } while(node && node.nodeType != dojo.dom.ELEMENT_NODE);

    if(node && tagName && tagName.toLowerCase() != node.tagName.toLowerCase()) {
        return dojo.dom.prevElement(node, tagName);
    }
    return node;
}

// TODO: hmph
/*this.forEachChildTag = function(node, unaryFunc) {
    var child = this.getFirstChildTag(node);
    while(child) {
        if(unaryFunc(child) == "break") { break; }
        child = this.getNextSiblingTag(child);
    }
}*/

dojo.dom.moveChildren = function(srcNode, destNode, trim){
    var count = 0;
    if(trim) {
        while(srcNode.hasChildNodes() &&
            srcNode.firstChild.nodeType == dojo.dom.TEXT_NODE) {
            srcNode.removeChild(srcNode.firstChild);
        }
        while(srcNode.hasChildNodes() &&
            srcNode.lastChild.nodeType == dojo.dom.TEXT_NODE) {
            srcNode.removeChild(srcNode.lastChild);
        }
    }
    while(srcNode.hasChildNodes()){
        destNode.appendChild(srcNode.firstChild);
        count++;
    }
    return count;
}

dojo.dom.copyChildren = function(srcNode, destNode, trim){
    var clonedNode = srcNode.cloneNode(true);
    return this.moveChildren(clonedNode, destNode, trim);
}

dojo.dom.removeChildren = function(node){
    var count = node.childNodes.length;
    while(node.hasChildNodes()){ node.removeChild(node.firstChild); }
    return count;
}

dojo.dom.replaceChildren = function(node, newChild){
    // FIXME: what if newChild is an array-like object?
    dojo.dom.removeChildren(node);
    node.appendChild(newChild);
}

dojo.dom.removeNode = function(node){
    if(node && node.parentNode){
        // return a ref to the removed child
        return node.parentNode.removeChild(node);
    }
}

dojo.dom.getAncestors = function(node, filterFunction, returnFirstHit) {
    var ancestors = [];
    var isFunction = (filterFunction && (filterFunction instanceof Function || typeof filterFunction == "function"));
    while(node) {
        if (!isFunction || filterFunction(node)) {
            ancestors.push(node);
        }
        if (returnFirstHit && ancestors.length > 0) { return ancestors[0]; }
        
        node = node.parentNode;
    }
    if (returnFirstHit) { return null; }
    return ancestors;
}

dojo.dom.getAncestorsByTag = function(node, tag, returnFirstHit) {
    tag = tag.toLowerCase();
    return dojo.dom.getAncestors(node, function(el){
        return ((el.tagName)&&(el.tagName.toLowerCase() == tag));
    }, returnFirstHit);
}

dojo.dom.getFirstAncestorByTag = function(node, tag) {
    return dojo.dom.getAncestorsByTag(node, tag, true);
}

dojo.dom.isDescendantOf = function(node, ancestor, guaranteeDescendant){
    // guaranteeDescendant allows us to be a "true" isDescendantOf function
    if(guaranteeDescendant && node) { node = node.parentNode; }
    while(node) {
        if(node == ancestor){ return true; }
        node = node.parentNode;
    }
    return false;
}

dojo.dom.innerXML = function(node){
    if(node.innerXML){
        return node.innerXML;
    }else if (node.xml){
        return node.xml;
    }else if(typeof XMLSerializer != "undefined"){
        return (new XMLSerializer()).serializeToString(node);
    }
}

dojo.dom.createDocument = function(){
    var doc = null;
    var _document = dojo.doc();

    if(!dj_undef("ActiveXObject")){
        var prefixes = [ "MSXML2", "Microsoft", "MSXML", "MSXML3" ];
        for(var i = 0; i<prefixes.length; i++){
            try{
                doc = new ActiveXObject(prefixes[i]+".XMLDOM");
            }catch(e){ /* squelch */ };

            if(doc){ break; }
        }
    }else if((_document.implementation)&&
        (_document.implementation.createDocument)){
        doc = _document.implementation.createDocument("", "", null);
    }
    
    return doc;
}

dojo.dom.createDocumentFromText = function(str, mimetype){
    if(!mimetype){ mimetype = "text/xml"; }
    if(!dj_undef("DOMParser")){
        var parser = new DOMParser();
        return parser.parseFromString(str, mimetype);
    }else if(!dj_undef("ActiveXObject")){
        var domDoc = dojo.dom.createDocument();
        if(domDoc){
            domDoc.async = false;
            domDoc.loadXML(str);
            return domDoc;
        }else{
            dojo.debug("toXml didn't work?");
        }
    /*
    }else if((dojo.render.html.capable)&&(dojo.render.html.safari)){
        // FIXME: this doesn't appear to work!
        // from: http://web-graphics.com/mtarchive/001606.php
        // var xml = '<?xml version="1.0"?>'+str;
        var mtype = "text/xml";
        var xml = '<?xml version="1.0"?>'+str;
        var url = "data:"+mtype+";charset=utf-8,"+encodeURIComponent(xml);
        var req = new XMLHttpRequest();
        req.open("GET", url, false);
        req.overrideMimeType(mtype);
        req.send(null);
        return req.responseXML;
    */
    }else{
        _document = dojo.doc();
        if(_document.createElement){
            // FIXME: this may change all tags to uppercase!
            var tmp = _document.createElement("xml");
            tmp.innerHTML = str;
            if(_document.implementation && _document.implementation.createDocument) {
                var xmlDoc = _document.implementation.createDocument("foo", "", null);
                for(var i = 0; i < tmp.childNodes.length; i++) {
                    xmlDoc.importNode(tmp.childNodes.item(i), true);
                }
                return xmlDoc;
            }
            // FIXME: probably not a good idea to have to return an HTML fragment
            // FIXME: the tmp.doc.firstChild is as tested from IE, so it may not
            // work that way across the board
            return ((tmp.document)&&
                (tmp.document.firstChild ?  tmp.document.firstChild : tmp));
        }
    }
    return null;
}

dojo.dom.prependChild = function(node, parent) {
    if(parent.firstChild) {
        parent.insertBefore(node, parent.firstChild);
    } else {
        parent.appendChild(node);
    }
    return true;
}

dojo.dom.insertBefore = function(node, ref, force){
    if (force != true &&
        (node === ref || node.nextSibling === ref)){ return false; }
    var parent = ref.parentNode;
    parent.insertBefore(node, ref);
    return true;
}

dojo.dom.insertAfter = function(node, ref, force){
    var pn = ref.parentNode;
    if(ref == pn.lastChild){
        if((force != true)&&(node === ref)){
            return false;
        }
        pn.appendChild(node);
    }else{
        return this.insertBefore(node, ref.nextSibling, force);
    }
    return true;
}

dojo.dom.insertAtPosition = function(node, ref, position){
    if((!node)||(!ref)||(!position)){ return false; }
    switch(position.toLowerCase()){
        case "before":
            return dojo.dom.insertBefore(node, ref);
        case "after":
            return dojo.dom.insertAfter(node, ref);
        case "first":
            if(ref.firstChild){
                return dojo.dom.insertBefore(node, ref.firstChild);
            }else{
                ref.appendChild(node);
                return true;
            }
            break;
        default: // aka: last
            ref.appendChild(node);
            return true;
    }
}

dojo.dom.insertAtIndex = function(node, containingNode, insertionIndex){
    var siblingNodes = containingNode.childNodes;

    // if there aren't any kids yet, just add it to the beginning

    if (!siblingNodes.length){
        containingNode.appendChild(node);
        return true;
    }

    // otherwise we need to walk the childNodes
    // and find our spot

    var after = null;

    for(var i=0; i<siblingNodes.length; i++){

        var sibling_index = siblingNodes.item(i)["getAttribute"] ? parseInt(siblingNodes.item(i).getAttribute("dojoinsertionindex")) : -1;

        if (sibling_index < insertionIndex){
            after = siblingNodes.item(i);
        }
    }

    if (after){
        // add it after the node in {after}

        return dojo.dom.insertAfter(node, after);
    }else{
        // add it to the start

        return dojo.dom.insertBefore(node, siblingNodes.item(0));
    }
}
    
/**
 * implementation of the DOM Level 3 attribute.
 * 
 * @param node The node to scan for text
 * @param text Optional, set the text to this value.
 */
dojo.dom.textContent = function(node, text){
    if (text) {
        var _document = dojo.doc();
        dojo.dom.replaceChildren(node, _document.createTextNode(text));
        return text;
    } else {
        var _result = "";
        if (node == null) { return _result; }
        for (var i = 0; i < node.childNodes.length; i++) {
            switch (node.childNodes[i].nodeType) {
                case 1: // ELEMENT_NODE
                case 5: // ENTITY_REFERENCE_NODE
                    _result += dojo.dom.textContent(node.childNodes[i]);
                    break;
                case 3: // TEXT_NODE
                case 2: // ATTRIBUTE_NODE
                case 4: // CDATA_SECTION_NODE
                    _result += node.childNodes[i].nodeValue;
                    break;
                default:
                    break;
            }
        }
        return _result;
    }
}

dojo.dom.hasParent = function (node) {
    return node && node.parentNode && dojo.dom.isNode(node.parentNode);
}

/**
 * Determines if node has any of the provided tag names and
 * returns the tag name that matches, empty string otherwise.
 *
 * Examples:
 *
 * myFooNode = <foo />
 * isTag(myFooNode, "foo"); // returns "foo"
 * isTag(myFooNode, "bar"); // returns ""
 * isTag(myFooNode, "FOO"); // returns ""
 * isTag(myFooNode, "hey", "foo", "bar"); // returns "foo"
**/
dojo.dom.isTag = function(node /* ... */) {
    if(node && node.tagName) {
        for(var i=1; i<arguments.length; i++){
            if(node.tagName==String(arguments[i])){
                return String(arguments[i]);
            }
        }
    }
    return "";
}

/** 
 * Implements DOM Level 2 setAttributeNS so it works cross browser.
 *
 * Example:
 * dojo.dom.setAttributeNS(domElem, "http://foobar.com/2006/someSpec", 
 *                             "hs:level", 3);
 */
dojo.dom.setAttributeNS = function(elem, namespaceURI, attrName, attrValue){
    if(elem == null || ((elem == undefined)&&(typeof elem == "undefined"))){
        dojo.raise("No element given to dojo.dom.setAttributeNS");
    }
    
    if(!((elem.setAttributeNS == undefined)&&(typeof elem.setAttributeNS == "undefined"))){ // w3c
        elem.setAttributeNS(namespaceURI, attrName, attrValue);
    }else{ // IE
        // get a root XML document
        var ownerDoc = elem.ownerDocument;
        var attribute = ownerDoc.createNode(
            2, // node type
            attrName,
            namespaceURI
        );
        
        // set value
        attribute.nodeValue = attrValue;
        
        // attach to element
        elem.setAttributeNode(attribute);
    }
}

dojo.provide("dojo.undo.browser");

try{
    if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
        document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+'iframe_history.html')+"'></iframe>");
    }
}catch(e){/* squelch */}

if(dojo.render.html.opera){
    dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}

/* NOTES:
 *  Safari 1.2: 
 *    back button "works" fine, however it's not possible to actually
 *    DETECT that you've moved backwards by inspecting window.location.
 *    Unless there is some other means of locating.
 *    FIXME: perhaps we can poll on history.length?
 *  Safari 2.0.3+ (and probably 1.3.2+):
 *    works fine, except when changeUrl is used. When changeUrl is used,
 *    Safari jumps all the way back to whatever page was shown before
 *    the page that uses dojo.undo.browser support.
 *  IE 5.5 SP2:
 *    back button behavior is macro. It does not move back to the
 *    previous hash value, but to the last full page load. This suggests
 *    that the iframe is the correct way to capture the back button in
 *    these cases.
 *    Don't test this page using local disk for MSIE. MSIE will not create 
 *    a history list for iframe_history.html if served from a file: URL. 
 *    The XML served back from the XHR tests will also not be properly 
 *    created if served from local disk. Serve the test pages from a web 
 *    server to test in that browser.
 *  IE 6.0:
 *    same behavior as IE 5.5 SP2
 * Firefox 1.0:
 *    the back button will return us to the previous hash on the same
 *    page, thereby not requiring an iframe hack, although we do then
 *    need to run a timer to detect inter-page movement.
 */
dojo.undo.browser = {
    initialHref: window.location.href,
    initialHash: window.location.hash,

    moveForward: false,
    historyStack: [],
    forwardStack: [],
    historyIframe: null,
    bookmarkAnchor: null,
    locationTimer: null,

    /**
     * setInitialState sets the state object and back callback for the very first page that is loaded.
     * It is recommended that you call this method as part of an event listener that is registered via
     * dojo.addOnLoad().
     */
    setInitialState: function(args){
        this.initialState = {"url": this.initialHref, "kwArgs": args, "urlHash": this.initialHash};
    },

    //FIXME: Would like to support arbitrary back/forward jumps. Have to rework iframeLoaded among other things.
    //FIXME: is there a slight race condition in moz using change URL with the timer check and when
    //       the hash gets set? I think I have seen a back/forward call in quick succession, but not consistent.
    /**
     * addToHistory takes one argument, and it is an object that defines the following functions:
     * - To support getting back button notifications, the object argument should implement a
     *   function called either "back", "backButton", or "handle". The string "back" will be
     *   passed as the first and only argument to this callback.
     * - To support getting forward button notifications, the object argument should implement a
     *   function called either "forward", "forwardButton", or "handle". The string "forward" will be
     *   passed as the first and only argument to this callback.
     * - If you want the browser location string to change, define "changeUrl" on the object. If the
     *   value of "changeUrl" is true, then a unique number will be appended to the URL as a fragment
     *   identifier (http://some.domain.com/path#uniquenumber). If it is any other value that does
     *   not evaluate to false, that value will be used as the fragment identifier. For example,
     *   if changeUrl: 'page1', then the URL will look like: http://some.domain.com/path#page1
     *   
     * Full example:
     * 
     * dojo.undo.browser.addToHistory({
     *   back: function() { alert('back pressed'); },
     *   forward: function() { alert('forward pressed'); },
     *   changeUrl: true
     * });
     */
    addToHistory: function(args){
        var hash = null;
        if(!this.historyIframe){
            this.historyIframe = window.frames["djhistory"];
        }
        if(!this.bookmarkAnchor){
            this.bookmarkAnchor = document.createElement("a");
            dojo.body().appendChild(this.bookmarkAnchor);
            this.bookmarkAnchor.style.display = "none";
        }
        if((!args["changeUrl"])||(dojo.render.html.ie)){
            var url = dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
            this.moveForward = true;
            dojo.io.setIFrameSrc(this.historyIframe, url, false);
        }
        if(args["changeUrl"]){
            this.changingUrl = true;
            hash = "#"+ ((args["changeUrl"]!==true) ? args["changeUrl"] : (new Date()).getTime());
            setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;", 1);
            this.bookmarkAnchor.href = hash;
            
            if(dojo.render.html.ie){
                var oldCB = args["back"]||args["backButton"]||args["handle"];

                //The function takes handleName as a parameter, in case the
                //callback we are overriding was "handle". In that case,
                //we will need to pass the handle name to handle.
                var tcb = function(handleName){
                    if(window.location.hash != ""){
                        setTimeout("window.location.href = '"+hash+"';", 1);
                    }
                    //Use apply to set "this" to args, and to try to avoid memory leaks.
                    oldCB.apply(this, [handleName]);
                }
        
                //Set interceptor function in the right place.
                if(args["back"]){
                    args.back = tcb;
                }else if(args["backButton"]){
                    args.backButton = tcb;
                }else if(args["handle"]){
                    args.handle = tcb;
                }
        
                //If addToHistory is called, then that means we prune the
                //forward stack -- the user went back, then wanted to
                //start a new forward path.
                this.forwardStack = []; 
                var oldFW = args["forward"]||args["forwardButton"]||args["handle"];
        
                //The function takes handleName as a parameter, in case the
                //callback we are overriding was "handle". In that case,
                //we will need to pass the handle name to handle.
                var tfw = function(handleName){
                    if(window.location.hash != ""){
                        window.location.href = hash;
                    }
                    if(oldFW){ // we might not actually have one
                        //Use apply to set "this" to args, and to try to avoid memory leaks.
                        oldFW.apply(this, [handleName]);
                    }
                }

                //Set interceptor function in the right place.
                if(args["forward"]){
                    args.forward = tfw;
                }else if(args["forwardButton"]){
                    args.forwardButton = tfw;
                }else if(args["handle"]){
                    args.handle = tfw;
                }

            }else if(dojo.render.html.moz){
                // start the timer
                if(!this.locationTimer){
                    this.locationTimer = setInterval("dojo.undo.browser.checkLocation();", 200);
                }
            }
        }

        this.historyStack.push({"url": url, "kwArgs": args, "urlHash": hash});
    },

    checkLocation: function(){
        if (!this.changingUrl){
            var hsl = this.historyStack.length;

            if((window.location.hash == this.initialHash||window.location.href == this.initialHref)&&(hsl == 1)){
                // FIXME: could this ever be a forward button?
                // we can't clear it because we still need to check for forwards. Ugg.
                // clearInterval(this.locationTimer);
                this.handleBackButton();
                return;
            }
            // first check to see if we could have gone forward. We always halt on
            // a no-hash item.
            if(this.forwardStack.length > 0){
                if(this.forwardStack[this.forwardStack.length-1].urlHash == window.location.hash){
                    this.handleForwardButton();
                    return;
                }
            }
    
            // ok, that didn't work, try someplace back in the history stack
            if((hsl >= 2)&&(this.historyStack[hsl-2])){
                if(this.historyStack[hsl-2].urlHash==window.location.hash){
                    this.handleBackButton();
                    return;
                }
            }
        }
    },

    iframeLoaded: function(evt, ifrLoc){
        if(!dojo.render.html.opera){
            var query = this._getUrlQuery(ifrLoc.href);
            if(query == null){ 
                // alert("iframeLoaded");
                // we hit the end of the history, so we should go back
                if(this.historyStack.length == 1){
                    this.handleBackButton();
                }
                return;
            }
            if(this.moveForward){
                // we were expecting it, so it's not either a forward or backward movement
                this.moveForward = false;
                return;
            }
    
            //Check the back stack first, since it is more likely.
            //Note that only one step back or forward is supported.
            if(this.historyStack.length >= 2 && query == this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
                this.handleBackButton();
            }
            else if(this.forwardStack.length > 0 && query == this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
                this.handleForwardButton();
            }
        }
    },

    handleBackButton: function(){
        //The "current" page is always at the top of the history stack.
        var current = this.historyStack.pop();
        if(!current){ return; }
        var last = this.historyStack[this.historyStack.length-1];
        if(!last && this.historyStack.length == 0){
            last = this.initialState;
        }
        if (last){
            if(last.kwArgs["back"]){
                last.kwArgs["back"]();
            }else if(last.kwArgs["backButton"]){
                last.kwArgs["backButton"]();
            }else if(last.kwArgs["handle"]){
                last.kwArgs.handle("back");
            }
        }
        this.forwardStack.push(current);
    },

    handleForwardButton: function(){
        var last = this.forwardStack.pop();
        if(!last){ return; }
        if(last.kwArgs["forward"]){
            last.kwArgs.forward();
        }else if(last.kwArgs["forwardButton"]){
            last.kwArgs.forwardButton();
        }else if(last.kwArgs["handle"]){
            last.kwArgs.handle("forward");
        }
        this.historyStack.push(last);
    },

    _getUrlQuery: function(url){
        var segments = url.split("?");
        if (segments.length < 2){
            return null;
        }
        else{
            return segments[1];
        }
    }
}

dojo.provide("dojo.io.BrowserIO");


dojo.io.checkChildrenForFile = function(node){
    var hasFile = false;
    var inputs = node.getElementsByTagName("input");
    dojo.lang.forEach(inputs, function(input){
        if(hasFile){ return; }
        if(input.getAttribute("type")=="file"){
            hasFile = true;
        }
    });
    return hasFile;
}

dojo.io.formHasFile = function(formNode){
    return dojo.io.checkChildrenForFile(formNode);
}

dojo.io.updateNode = function(node, urlOrArgs){
    node = dojo.byId(node);
    var args = urlOrArgs;
    if(dojo.lang.isString(urlOrArgs)){
        args = { url: urlOrArgs };
    }
    args.mimetype = "text/html";
    args.load = function(t, d, e){
        while(node.firstChild){
            if(dojo["event"]){
                try{
                    dojo.event.browser.clean(node.firstChild);
                }catch(e){}
            }
            node.removeChild(node.firstChild);
        }
        node.innerHTML = d;
    };
    dojo.io.bind(args);
}

dojo.io.formFilter = function(node) {
    var type = (node.type||"").toLowerCase();
    return !node.disabled && node.name
        && !dojo.lang.inArray(["file", "submit", "image", "reset", "button"], type);
}

// TODO: Move to htmlUtils
dojo.io.encodeForm = function(formNode, encoding, formFilter){
    if((!formNode)||(!formNode.tagName)||(!formNode.tagName.toLowerCase() == "form")){
        dojo.raise("Attempted to encode a non-form element.");
    }
    if(!formFilter) { formFilter = dojo.io.formFilter; }
    var enc = /utf/i.test(encoding||"") ? encodeURIComponent : dojo.string.encodeAscii;
    var values = [];

    for(var i = 0; i < formNode.elements.length; i++){
        var elm = formNode.elements[i];
        if(!elm || elm.tagName.toLowerCase() == "fieldset" || !formFilter(elm)) { continue; }
        var name = enc(elm.name);
        var type = elm.type.toLowerCase();

        if(type == "select-multiple"){
            for(var j = 0; j < elm.options.length; j++){
                if(elm.options[j].selected) {
                    values.push(name + "=" + enc(elm.options[j].value));
                }
            }
        }else if(dojo.lang.inArray(["radio", "checkbox"], type)){
            if(elm.checked){
                values.push(name + "=" + enc(elm.value));
            }
        }else{
            values.push(name + "=" + enc(elm.value));
        }
    }

    // now collect input type="image", which doesn't show up in the elements array
    var inputs = formNode.getElementsByTagName("input");
    for(var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        if(input.type.toLowerCase() == "image" && input.form == formNode
            && formFilter(input)) {
            var name = enc(input.name);
            values.push(name + "=" + enc(input.value));
            values.push(name + ".x=0");
            values.push(name + ".y=0");
        }
    }
    return values.join("&") + "&";
}

dojo.io.FormBind = function(args) {
    this.bindArgs = {};

    if(args && args.formNode) {
        this.init(args);
    } else if(args) {
        this.init({formNode: args});
    }
}
dojo.lang.extend(dojo.io.FormBind, {
    form: null,

    bindArgs: null,

    clickedButton: null,

    init: function(args) {
        var form = dojo.byId(args.formNode);

        if(!form || !form.tagName || form.tagName.toLowerCase() != "form") {
            throw new Error("FormBind: Couldn't apply, invalid form");
        } else if(this.form == form) {
            return;
        } else if(this.form) {
            throw new Error("FormBind: Already applied to a form");
        }

        dojo.lang.mixin(this.bindArgs, args);
        this.form = form;

        this.connect(form, "onsubmit", "submit");

        for(var i = 0; i < form.elements.length; i++) {
            var node = form.elements[i];
            if(node && node.type && dojo.lang.inArray(["submit", "button"], node.type.toLowerCase())) {
                this.connect(node, "onclick", "click");
            }
        }

        var inputs = form.getElementsByTagName("input");
        for(var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            if(input.type.toLowerCase() == "image" && input.form == form) {
                this.connect(input, "onclick", "click");
            }
        }
    },

    onSubmit: function(form) {
        return true;
    },

    submit: function(e) {
        e.preventDefault();
        if(this.onSubmit(this.form)) {
            dojo.io.bind(dojo.lang.mixin(this.bindArgs, {
                formFilter: dojo.lang.hitch(this, "formFilter")
            }));
        }
    },

    click: function(e) {
        var node = e.currentTarget;
        if(node.disabled) { return; }
        this.clickedButton = node;
    },

    formFilter: function(node) {
        var type = (node.type||"").toLowerCase();
        var accept = false;
        if(node.disabled || !node.name) {
            accept = false;
        } else if(dojo.lang.inArray(["submit", "button", "image"], type)) {
            if(!this.clickedButton) { this.clickedButton = node; }
            accept = node == this.clickedButton;
        } else {
            accept = !dojo.lang.inArray(["file", "submit", "reset", "button"], type);
        }
        return accept;
    },

    // in case you don't have dojo.event.* pulled in
    connect: function(srcObj, srcFcn, targetFcn) {
        if(dojo.evalObjPath("dojo.event.connect")) {
            dojo.event.connect(srcObj, srcFcn, this, targetFcn);
        } else {
            var fcn = dojo.lang.hitch(this, targetFcn);
            srcObj[srcFcn] = function(e) {
                if(!e) { e = window.event; }
                if(!e.currentTarget) { e.currentTarget = e.srcElement; }
                if(!e.preventDefault) { e.preventDefault = function() { window.event.returnValue = false; } }
                fcn(e);
            }
        }
    }
});

dojo.io.XMLHTTPTransport = new function(){
    var _this = this;

    var _cache = {}; // FIXME: make this public? do we even need to?
    this.useCache = false; // if this is true, we'll cache unless kwArgs.useCache = false
    this.preventCache = false; // if this is true, we'll always force GET requests to cache

    // FIXME: Should this even be a function? or do we just hard code it in the next 2 functions?
    function getCacheKey(url, query, method) {
        return url + "|" + query + "|" + method.toLowerCase();
    }

    function addToCache(url, query, method, http) {
        _cache[getCacheKey(url, query, method)] = http;
    }

    function getFromCache(url, query, method) {
        return _cache[getCacheKey(url, query, method)];
    }

    this.clearCache = function() {
        _cache = {};
    }

    // moved successful load stuff here
    function doLoad(kwArgs, http, url, query, useCache) {
        if(    ((http.status>=200)&&(http.status<300))||     // allow any 2XX response code
            (http.status==304)||                         // get it out of the cache
            (location.protocol=="file:" && (http.status==0 || http.status==undefined))||
            (location.protocol=="chrome:" && (http.status==0 || http.status==undefined))
        ){
            var ret;
            if(kwArgs.method.toLowerCase() == "head"){
                var headers = http.getAllResponseHeaders();
                ret = {};
                ret.toString = function(){ return headers; }
                var values = headers.split(/[\r\n]+/g);
                for(var i = 0; i < values.length; i++) {
                    var pair = values[i].match(/^([^:]+)\s*:\s*(.+)$/i);
                    if(pair) {
                        ret[pair[1]] = pair[2];
                    }
                }
            }else if(kwArgs.mimetype == "text/javascript"){
                try{
                    ret = dj_eval(http.responseText);
                }catch(e){
                    dojo.debug(e);
                    dojo.debug(http.responseText);
                    ret = null;
                }
            }else if(kwArgs.mimetype == "text/json"){
                try{
                    ret = dj_eval("("+http.responseText+")");
                }catch(e){
                    dojo.debug(e);
                    dojo.debug(http.responseText);
                    ret = false;
                }
            }else if((kwArgs.mimetype == "application/xml")||
                        (kwArgs.mimetype == "text/xml")){
                ret = http.responseXML;
                if(!ret || typeof ret == "string" || !http.getResponseHeader("Content-Type")) {
                    ret = dojo.dom.createDocumentFromText(http.responseText);
                }
            }else{
                ret = http.responseText;
            }

            if(useCache){ // only cache successful responses
                addToCache(url, query, kwArgs.method, http);
            }
            kwArgs[(typeof kwArgs.load == "function") ? "load" : "handle"]("load", ret, http, kwArgs);
        }else{
            var errObj = new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
            kwArgs[(typeof kwArgs.error == "function") ? "error" : "handle"]("error", errObj, http, kwArgs);
        }
    }

    // set headers (note: Content-Type will get overriden if kwArgs.contentType is set)
    function setHeaders(http, kwArgs){
        if(kwArgs["headers"]) {
            for(var header in kwArgs["headers"]) {
                if(header.toLowerCase() == "content-type" && !kwArgs["contentType"]) {
                    kwArgs["contentType"] = kwArgs["headers"][header];
                } else {
                    http.setRequestHeader(header, kwArgs["headers"][header]);
                }
            }
        }
    }

    this.inFlight = [];
    this.inFlightTimer = null;

    this.startWatchingInFlight = function(){
        if(!this.inFlightTimer){
            this.inFlightTimer = setInterval("dojo.io.XMLHTTPTransport.watchInFlight();", 10);
        }
    }

    this.watchInFlight = function(){
        var now = null;
        for(var x=this.inFlight.length-1; x>=0; x--){
            var tif = this.inFlight[x];
            if(!tif){ this.inFlight.splice(x, 1); continue; }
            if(4==tif.http.readyState){
                // remove it so we can clean refs
                this.inFlight.splice(x, 1);
                doLoad(tif.req, tif.http, tif.url, tif.query, tif.useCache);
            }else if (tif.startTime){
                //See if this is a timeout case.
                if(!now){
                    now = (new Date()).getTime();
                }
                if(tif.startTime + (tif.req.timeoutSeconds * 1000) < now){
                    //Stop the request.
                    if(typeof tif.http.abort == "function"){
                        tif.http.abort();
                    }

                    // remove it so we can clean refs
                    this.inFlight.splice(x, 1);
                    tif.req[(typeof tif.req.timeout == "function") ? "timeout" : "handle"]("timeout", null, tif.http, tif.req);
                }
            }
        }

        if(this.inFlight.length == 0){
            clearInterval(this.inFlightTimer);
            this.inFlightTimer = null;
        }
    }

    var hasXmlHttp = dojo.hostenv.getXmlhttpObject() ? true : false;
    this.canHandle = function(kwArgs){
        // canHandle just tells dojo.io.bind() if this is a good transport to
        // use for the particular type of request.

        // FIXME: we need to determine when form values need to be
        // multi-part mime encoded and avoid using this transport for those
        // requests.
        return hasXmlHttp
            && dojo.lang.inArray(["text/plain", "text/html", "application/xml", "text/xml", "text/javascript", "text/json"], (kwArgs["mimetype"].toLowerCase()||""))
            && !( kwArgs["formNode"] && dojo.io.formHasFile(kwArgs["formNode"]) );
    }

    this.multipartBoundary = "45309FFF-BD65-4d50-99C9-36986896A96F";    // unique guid as a boundary value for multipart posts

    this.bind = function(kwArgs){
        if(!kwArgs["url"]){
            // are we performing a history action?
            if( !kwArgs["formNode"]
                && (kwArgs["backButton"] || kwArgs["back"] || kwArgs["changeUrl"] || kwArgs["watchForURL"])
                && (!djConfig.preventBackButtonFix)) {
        dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request",
                        "Use dojo.undo.browser.addToHistory() instead.", "0.4");
                dojo.undo.browser.addToHistory(kwArgs);
                return true;
            }
        }

        // build this first for cache purposes
        var url = kwArgs.url;
        var query = "";
        if(kwArgs["formNode"]){
            var ta = kwArgs.formNode.getAttribute("action");
            if((ta)&&(!kwArgs["url"])){ url = ta; }
            var tp = kwArgs.formNode.getAttribute("method");
            if((tp)&&(!kwArgs["method"])){ kwArgs.method = tp; }
            query += dojo.io.encodeForm(kwArgs.formNode, kwArgs.encoding, kwArgs["formFilter"]);
        }

        if(url.indexOf("#") > -1) {
            dojo.debug("Warning: dojo.io.bind: stripping hash values from url:", url);
            url = url.split("#")[0];
        }

        if(kwArgs["file"]){
            // force post for file transfer
            kwArgs.method = "post";
        }

        if(!kwArgs["method"]){
            kwArgs.method = "get";
        }

        // guess the multipart value        
        if(kwArgs.method.toLowerCase() == "get"){
            // GET cannot use multipart
            kwArgs.multipart = false;
        }else{
            if(kwArgs["file"]){
                // enforce multipart when sending files
                kwArgs.multipart = true;
            }else if(!kwArgs["multipart"]){
                // default 
                kwArgs.multipart = false;
            }
        }

        if(kwArgs["backButton"] || kwArgs["back"] || kwArgs["changeUrl"]){
            dojo.undo.browser.addToHistory(kwArgs);
        }

        var content = kwArgs["content"] || {};

        if(kwArgs.sendTransport) {
            content["dojo.transport"] = "xmlhttp";
        }

        do { // break-block
            if(kwArgs.postContent){
                query = kwArgs.postContent;
                break;
            }

            if(content) {
                query += dojo.io.argsFromMap(content, kwArgs.encoding);
            }
            
            if(kwArgs.method.toLowerCase() == "get" || !kwArgs.multipart){
                break;
            }

            var    t = [];
            if(query.length){
                var q = query.split("&");
                for(var i = 0; i < q.length; ++i){
                    if(q[i].length){
                        var p = q[i].split("=");
                        t.push(    "--" + this.multipartBoundary,
                                "Content-Disposition: form-data; name=\"" + p[0] + "\"", 
                                "",
                                p[1]);
                    }
                }
            }

            if(kwArgs.file){
                if(dojo.lang.isArray(kwArgs.file)){
                    for(var i = 0; i < kwArgs.file.length; ++i){
                        var o = kwArgs.file[i];
                        t.push(    "--" + this.multipartBoundary,
                                "Content-Disposition: form-data; name=\"" + o.name + "\"; filename=\"" + ("fileName" in o ? o.fileName : o.name) + "\"",
                                "Content-Type: " + ("contentType" in o ? o.contentType : "application/octet-stream"),
                                "",
                                o.content);
                    }
                }else{
                    var o = kwArgs.file;
                    t.push(    "--" + this.multipartBoundary,
                            "Content-Disposition: form-data; name=\"" + o.name + "\"; filename=\"" + ("fileName" in o ? o.fileName : o.name) + "\"",
                            "Content-Type: " + ("contentType" in o ? o.contentType : "application/octet-stream"),
                            "",
                            o.content);
                }
            }

            if(t.length){
                t.push("--"+this.multipartBoundary+"--", "");
                query = t.join("\r\n");
            }
        }while(false);

        // kwArgs.Connection = "close";

        var async = kwArgs["sync"] ? false : true;

        var preventCache = kwArgs["preventCache"] ||
            (this.preventCache == true && kwArgs["preventCache"] != false);
        var useCache = kwArgs["useCache"] == true ||
            (this.useCache == true && kwArgs["useCache"] != false );

        // preventCache is browser-level (add query string junk), useCache
        // is for the local cache. If we say preventCache, then don't attempt
        // to look in the cache, but if useCache is true, we still want to cache
        // the response
        if(!preventCache && useCache){
            var cachedHttp = getFromCache(url, query, kwArgs.method);
            if(cachedHttp){
                doLoad(kwArgs, cachedHttp, url, query, false);
                return;
            }
        }

        // much of this is from getText, but reproduced here because we need
        // more flexibility
        var http = dojo.hostenv.getXmlhttpObject(kwArgs);    
        var received = false;

        // build a handler function that calls back to the handler obj
        if(async){
            var startTime = 
            // FIXME: setting up this callback handler leaks on IE!!!
            this.inFlight.push({
                "req":        kwArgs,
                "http":        http,
                "url":         url,
                "query":    query,
                "useCache":    useCache,
                "startTime": kwArgs.timeoutSeconds ? (new Date()).getTime() : 0
            });
            this.startWatchingInFlight();
        }

        if(kwArgs.method.toLowerCase() == "post"){
            // FIXME: need to hack in more flexible Content-Type setting here!
            http.open("POST", url, async);
            setHeaders(http, kwArgs);
            http.setRequestHeader("Content-Type", kwArgs.multipart ? ("multipart/form-data; boundary=" + this.multipartBoundary) : 
                (kwArgs.contentType || "application/x-www-form-urlencoded"));
            try{
                http.send(query);
            }catch(e){
                if(typeof http.abort == "function"){
                    http.abort();
                }
                doLoad(kwArgs, {status: 404}, url, query, useCache);
            }
        }else{
            var tmpUrl = url;
            if(query != "") {
                tmpUrl += (tmpUrl.indexOf("?") > -1 ? "&" : "?") + query;
            }
            if(preventCache) {
                tmpUrl += (dojo.string.endsWithAny(tmpUrl, "?", "&")
                    ? "" : (tmpUrl.indexOf("?") > -1 ? "&" : "?")) + "dojo.preventCache=" + new Date().valueOf();
            }
            http.open(kwArgs.method.toUpperCase(), tmpUrl, async);
            setHeaders(http, kwArgs);
            try {
                http.send(null);
            }catch(e)    {
                if(typeof http.abort == "function"){
                    http.abort();
                }
                doLoad(kwArgs, {status: 404}, url, query, useCache);
            }
        }

        if( !async ) {
            doLoad(kwArgs, http, url, query, useCache);
        }

        kwArgs.abort = function(){
            return http.abort();
        }

        return;
    }
    dojo.io.transports.addTransport("XMLHTTPTransport");
}

