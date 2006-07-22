/*
    Copyright (c) 2004-2006, The Dojo Foundation
    All Rights Reserved.

    Licensed under the Academic Free License version 2.1 or above OR the
    modified BSD license. For more information on Dojo licensing, see:

        http://dojotoolkit.org/community/licensing.shtml
*/

/*
    This is a compiled version of Dojo, built for deployment and not for
    development. To get an editable version, please visit:

        http://dojotoolkit.org

    for documentation and information on getting the source.
*/

if(typeof dojo=="undefined"){
var dj_global=this;
function dj_undef(_1,_2){
if(_2==null){
_2=dojo.global();
}
return (typeof _2[_1]=="undefined");
}
if(dj_undef("djConfig",this)){
var djConfig={};
}
if(dj_undef("dojo",this)){
var dojo={};
}
dojo._currentContext=this;
if(!dj_undef("document",dojo._currentContext)){
dojo._currentDocument=this.document;
}
dojo.version={major:0,minor:3,patch:1,flag:"svn-4855",revision:Number("$Rev: 4525 $".match(/[0-9]+/)[0]),toString:function(){
with(dojo.version){
return major+"."+minor+"."+patch+flag+" ("+revision+")";
}
}};
dojo.evalProp=function(_3,_4,_5){
return (_4&&!dj_undef(_3,_4)?_4[_3]:(_5?(_4[_3]={}):undefined));
};
dojo.parseObjPath=function(_6,_7,_8){
var _9=(_7!=null?_7:dj_global);
var _a=_6.split(".");
var _b=_a.pop();
for(var i=0,l=_a.length;i<l&&_9;i++){
_9=dojo.evalProp(_a[i],_9,_8);
}
return {obj:_9,prop:_b};
};
dojo.evalObjPath=function(_d,_e){
if(typeof _d!="string"){
return dj_global;
}
if(_d.indexOf(".")==-1){
return dojo.evalProp(_d,dj_global,_e);
}
var _f=dojo.parseObjPath(_d,dj_global,_e);
if(_f){
return dojo.evalProp(_f.prop,_f.obj,_e);
}
return null;
};
dojo.global=function(){
return dojo._currentContext;
};
dojo.doc=function(){
return dojo._currentDocument;
};
dojo.body=function(){
return dojo.doc().body||dojo.doc().getElementsByTagName("body")[0];
};
dojo.withGlobal=function(_10,_11,_12){
var _13=dojo._currentDocument;
var _14=dojo._currentContext;
var _15;
try{
dojo._currentContext=_10;
dojo._currentDocument=_10.document;
if(_12){
_15=dojo.lang.curryArguments(_12,_11,arguments,3);
}else{
_15=_11();
}
}
catch(e){
dojo._currentContext=_14;
dojo._currentDocument=_13;
throw e;
}
dojo._currentContext=_14;
dojo._currentDocument=_13;
return _15;
};
dojo.withDoc=function(_16,_17,_18){
var _19=this._currentDocument;
var _1a;
try{
dojo._currentDocument=_16;
if(_18){
_1a=dojo.lang.curryArguments(_18,_17,arguments,3);
}else{
_1a=_17();
}
}
catch(e){
dojo._currentDocument=_19;
throw e;
}
dojo._currentDocument=_19;
return _1a;
};
dojo.errorToString=function(_1b){
if(!dj_undef("message",_1b)){
return _1b.message;
}else{
if(!dj_undef("description",_1b)){
return _1b.description;
}else{
return _1b;
}
}
};
dojo.raise=function(_1c,_1d){
if(_1d){
_1c=_1c+": "+dojo.errorToString(_1d);
}
try{
dojo.hostenv.println("FATAL: "+_1c);
}
catch(e){
}
throw Error(_1c);
};
dojo.debug=function(){
};
dojo.debugShallow=function(obj){
};
dojo.profile={start:function(){
},end:function(){
},stop:function(){
},dump:function(){
}};
function dj_eval(_1f){
return dj_global.eval?dj_global.eval(_1f):eval(_1f);
}
dojo.unimplemented=function(_20,_21){
var _22="'"+_20+"' not implemented";
if(_21!=null){
_22+=" "+_21;
}
dojo.raise(_22);
};
dojo.deprecated=function(_23,_24,_25){
var _26="DEPRECATED: "+_23;
if(_24){
_26+=" "+_24;
}
if(_25){
_26+=" -- will be removed in version: "+_25;
}
dojo.debug(_26);
};
dojo.inherits=function(_27,_28){
if(typeof _28!="function"){
dojo.raise("dojo.inherits: superclass argument ["+_28+"] must be a function (subclass: ["+_27+"']");
}
_27.prototype=new _28();
_27.prototype.constructor=_27;
_27.superclass=_28.prototype;
_27["super"]=_28.prototype;
};
dojo.render=(function(){
function vscaffold(_29,_2a){
var tmp={capable:false,support:{builtin:false,plugin:false},prefixes:_29};
for(var i=0;i<_2a.length;i++){
tmp[_2a[i]]=false;
}
return tmp;
}
return {name:"",ver:dojo.version,os:{win:false,linux:false,osx:false},html:vscaffold(["html"],["ie","opera","khtml","safari","moz"]),svg:vscaffold(["svg"],["corel","adobe","batik"]),vml:vscaffold(["vml"],["ie"]),swf:vscaffold(["Swf","Flash","Mm"],["mm"]),swt:vscaffold(["Swt"],["ibm"])};
})();
dojo.hostenv=(function(){
var _2d={isDebug:false,allowQueryConfig:false,baseScriptUri:"",baseRelativePath:"",libraryScriptUri:"",iePreventClobber:false,ieClobberMinimal:true,preventBackButtonFix:true,searchIds:[],parseWidgets:true};
if(typeof djConfig=="undefined"){
djConfig=_2d;
}else{
for(var _2e in _2d){
if(typeof djConfig[_2e]=="undefined"){
djConfig[_2e]=_2d[_2e];
}
}
}
return {name_:"(unset)",version_:"(unset)",getName:function(){
return this.name_;
},getVersion:function(){
return this.version_;
},getText:function(uri){
dojo.unimplemented("getText","uri="+uri);
}};
})();
dojo.hostenv.getBaseScriptUri=function(){
if(djConfig.baseScriptUri.length){
return djConfig.baseScriptUri;
}
var uri=new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
if(!uri){
dojo.raise("Nothing returned by getLibraryScriptUri(): "+uri);
}
var _31=uri.lastIndexOf("/");
djConfig.baseScriptUri=djConfig.baseRelativePath;
return djConfig.baseScriptUri;
};
(function(){
var _32={pkgFileName:"__package__",loading_modules_:{},loaded_modules_:{},addedToLoadingCount:[],removedFromLoadingCount:[],inFlightCount:0,modulePrefixes_:{dojo:{name:"dojo",value:"src"}},setModulePrefix:function(_33,_34){
this.modulePrefixes_[_33]={name:_33,value:_34};
},getModulePrefix:function(_35){
var mp=this.modulePrefixes_;
if((mp[_35])&&(mp[_35]["name"])){
return mp[_35].value;
}
return _35;
},getTextStack:[],loadUriStack:[],loadedUris:[],post_load_:false,modulesLoadedListeners:[],unloadListeners:[],loadNotifying:false};
for(var _37 in _32){
dojo.hostenv[_37]=_32[_37];
}
})();
dojo.hostenv.loadPath=function(_38,_39,cb){
var uri;
if((_38.charAt(0)=="/")||(_38.match(/^\w+:/))){
uri=_38;
}else{
uri=this.getBaseScriptUri()+_38;
}
if(djConfig.cacheBust&&dojo.render.html.capable){
uri+="?"+String(djConfig.cacheBust).replace(/\W+/g,"");
}
try{
return ((!_39)?this.loadUri(uri,cb):this.loadUriAndCheck(uri,_39,cb));
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.hostenv.loadUri=function(uri,cb){
if(this.loadedUris[uri]){
return 1;
}
var _3e=this.getText(uri,null,true);
if(_3e==null){
return 0;
}
this.loadedUris[uri]=true;
if(cb){
_3e="("+_3e+")";
}
var _3f=dj_eval(_3e);
if(cb){
cb(_3f);
}
return 1;
};
dojo.hostenv.loadUriAndCheck=function(uri,_41,cb){
var ok=true;
try{
ok=this.loadUri(uri,cb);
}
catch(e){
dojo.debug("failed loading ",uri," with error: ",e);
}
return ((ok)&&(this.findModule(_41,false)))?true:false;
};
dojo.loaded=function(){
};
dojo.unloaded=function(){
};
dojo.hostenv.loaded=function(){
this.loadNotifying=true;
this.post_load_=true;
var mll=this.modulesLoadedListeners;
for(var x=0;x<mll.length;x++){
mll[x]();
}
this.modulesLoadedListeners=[];
this.loadNotifying=false;
dojo.loaded();
};
dojo.hostenv.unloaded=function(){
var mll=this.unloadListeners;
while(mll.length){
(mll.pop())();
}
dojo.unloaded();
};
dojo.addOnLoad=function(obj,_48){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.modulesLoadedListeners.push(obj);
}else{
if(arguments.length>1){
dh.modulesLoadedListeners.push(function(){
obj[_48]();
});
}
}
if(dh.post_load_&&dh.inFlightCount==0&&!dh.loadNotifying){
dh.callLoaded();
}
};
dojo.addOnUnload=function(obj,_4b){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.unloadListeners.push(obj);
}else{
if(arguments.length>1){
dh.unloadListeners.push(function(){
obj[_4b]();
});
}
}
};
dojo.hostenv.modulesLoaded=function(){
if(this.post_load_){
return;
}
if((this.loadUriStack.length==0)&&(this.getTextStack.length==0)){
if(this.inFlightCount>0){
dojo.debug("files still in flight!");
return;
}
dojo.hostenv.callLoaded();
}
};
dojo.hostenv.callLoaded=function(){
if(typeof setTimeout=="object"){
setTimeout("dojo.hostenv.loaded();",0);
}else{
dojo.hostenv.loaded();
}
};
dojo.hostenv.getModuleSymbols=function(_4d){
var _4e=_4d.split(".");
for(var i=_4e.length-1;i>0;i--){
var _50=_4e.slice(0,i).join(".");
var _51=this.getModulePrefix(_50);
if(_51!=_50){
_4e.splice(0,i,_51);
break;
}
}
return _4e;
};
dojo.hostenv._global_omit_module_check=false;
dojo.hostenv.loadModule=function(_52,_53,_54){
if(!_52){
return;
}
_54=this._global_omit_module_check||_54;
var _55=this.findModule(_52,false);
if(_55){
return _55;
}
if(dj_undef(_52,this.loading_modules_)){
this.addedToLoadingCount.push(_52);
}
this.loading_modules_[_52]=1;
var _56=_52.replace(/\./g,"/")+".js";
var _57=_52.split(".");
if(djConfig.autoLoadNamespace){
dojo.getNamespace(_57[0]);
}
var _58=this.getModuleSymbols(_52);
var _59=((_58[0].charAt(0)!="/")&&(!_58[0].match(/^\w+:/)));
var _5a=_58[_58.length-1];
if(_5a=="*"){
_52=(_57.slice(0,-1)).join(".");
while(_58.length){
_58.pop();
_58.push(this.pkgFileName);
_56=_58.join("/")+".js";
if(_59&&(_56.charAt(0)=="/")){
_56=_56.slice(1);
}
ok=this.loadPath(_56,((!_54)?_52:null));
if(ok){
break;
}
_58.pop();
}
}else{
_56=_58.join("/")+".js";
_52=_57.join(".");
var ok=this.loadPath(_56,((!_54)?_52:null));
if((!ok)&&(!_53)){
_58.pop();
while(_58.length){
_56=_58.join("/")+".js";
ok=this.loadPath(_56,((!_54)?_52:null));
if(ok){
break;
}
_58.pop();
_56=_58.join("/")+"/"+this.pkgFileName+".js";
if(_59&&(_56.charAt(0)=="/")){
_56=_56.slice(1);
}
ok=this.loadPath(_56,((!_54)?_52:null));
if(ok){
break;
}
}
}
if((!ok)&&(!_54)){
dojo.raise("Could not load '"+_52+"'; last tried '"+_56+"'");
}
}
if(!_54&&!this["isXDomain"]){
_55=this.findModule(_52,false);
if(!_55){
dojo.raise("symbol '"+_52+"' is not defined after loading '"+_56+"'");
}
}
return _55;
};
dojo.hostenv.startPackage=function(_5c){
var _5d=dojo.evalObjPath((_5c.split(".").slice(0,-1)).join("."));
this.loaded_modules_[(new String(_5c)).toLowerCase()]=_5d;
var _5e=_5c.split(/\./);
if(_5e[_5e.length-1]=="*"){
_5e.pop();
}
return dojo.evalObjPath(_5e.join("."),true);
};
dojo.hostenv.findModule=function(_5f,_60){
var lmn=(new String(_5f)).toLowerCase();
if(this.loaded_modules_[lmn]){
return this.loaded_modules_[lmn];
}
var _62=dojo.evalObjPath(_5f);
if((_5f)&&(typeof _62!="undefined")&&(_62)){
this.loaded_modules_[lmn]=_62;
return _62;
}
if(_60){
dojo.raise("no loaded module named '"+_5f+"'");
}
return null;
};
dojo.kwCompoundRequire=function(_63){
var _64=_63["common"]||[];
var _65=(_63[dojo.hostenv.name_])?_64.concat(_63[dojo.hostenv.name_]||[]):_64.concat(_63["default"]||[]);
for(var x=0;x<_65.length;x++){
var _67=_65[x];
if(_67.constructor==Array){
dojo.hostenv.loadModule.apply(dojo.hostenv,_67);
}else{
dojo.hostenv.loadModule(_67);
}
}
};
dojo.require=function(){
dojo.hostenv.loadModule.apply(dojo.hostenv,arguments);
};
dojo.requireIf=function(){
if((arguments[0]===true)||(arguments[0]=="common")||(arguments[0]&&dojo.render[arguments[0]].capable)){
var _68=[];
for(var i=1;i<arguments.length;i++){
_68.push(arguments[i]);
}
dojo.require.apply(dojo,_68);
}
};
dojo.requireAfterIf=dojo.requireIf;
dojo.provide=function(){
return dojo.hostenv.startPackage.apply(dojo.hostenv,arguments);
};
dojo.setModulePrefix=function(_6a,_6b){
return dojo.hostenv.setModulePrefix(_6a,_6b);
};
dojo.exists=function(obj,_6d){
var p=_6d.split(".");
for(var i=0;i<p.length;i++){
if(!(obj[p[i]])){
return false;
}
obj=obj[p[i]];
}
return true;
};
}
if(typeof window=="undefined"){
dojo.raise("no window object");
}
(function(){
if(djConfig.allowQueryConfig){
var _70=document.location.toString();
var _71=_70.split("?",2);
if(_71.length>1){
var _72=_71[1];
var _73=_72.split("&");
for(var x in _73){
var sp=_73[x].split("=");
if((sp[0].length>9)&&(sp[0].substr(0,9)=="djConfig.")){
var opt=sp[0].substr(9);
try{
djConfig[opt]=eval(sp[1]);
}
catch(e){
djConfig[opt]=sp[1];
}
}
}
}
}
if(((djConfig["baseScriptUri"]=="")||(djConfig["baseRelativePath"]==""))&&(document&&document.getElementsByTagName)){
var _77=document.getElementsByTagName("script");
var _78=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_77.length;i++){
var src=_77[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_78);
if(m){
var _7c=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_7c+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_7c;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_7c;
}
break;
}
}
}
var dr=dojo.render;
var drh=dojo.render.html;
var drs=dojo.render.svg;
var dua=(drh.UA=navigator.userAgent);
var dav=(drh.AV=navigator.appVersion);
var t=true;
var f=false;
drh.capable=t;
drh.support.builtin=t;
dr.ver=parseFloat(drh.AV);
dr.os.mac=dav.indexOf("Macintosh")>=0;
dr.os.win=dav.indexOf("Windows")>=0;
dr.os.linux=dav.indexOf("X11")>=0;
drh.opera=dua.indexOf("Opera")>=0;
drh.khtml=(dav.indexOf("Konqueror")>=0)||(dav.indexOf("Safari")>=0);
drh.safari=dav.indexOf("Safari")>=0;
var _84=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_84>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_84+6,_84+14);
}
drh.ie=(document.all)&&(!drh.opera);
drh.ie50=drh.ie&&dav.indexOf("MSIE 5.0")>=0;
drh.ie55=drh.ie&&dav.indexOf("MSIE 5.5")>=0;
drh.ie60=drh.ie&&dav.indexOf("MSIE 6.0")>=0;
drh.ie70=drh.ie&&dav.indexOf("MSIE 7.0")>=0;
dojo.locale=(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
dr.vml.capable=drh.ie;
drs.capable=f;
drs.support.plugin=f;
drs.support.builtin=f;
if(document.implementation&&document.implementation.hasFeature&&document.implementation.hasFeature("org.w3c.dom.svg","1.0")){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _85=null;
var _86=null;
try{
_85=new XMLHttpRequest();
}
catch(e){
}
if(!_85){
for(var i=0;i<3;++i){
var _88=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_85=new ActiveXObject(_88);
}
catch(e){
_86=e;
}
if(_85){
dojo.hostenv._XMLHTTP_PROGIDS=[_88];
break;
}
}
}
if(!_85){
return dojo.raise("XMLHTTP not available",_86);
}
return _85;
};
dojo.hostenv.getText=function(uri,_8a,_8b){
var _8c=this.getXmlhttpObject();
function isDocumentOk(_8d){
var _8e=_8d["status"];
return Boolean((!_8e)||((200<=_8e)&&(300>_8e))||(_8e==304));
}
if(_8a){
_8c.onreadystatechange=function(){
if(4==_8c.readyState){
if(isDocumentOk(_8c)){
_8a(_8c.responseText);
}
}
};
}
_8c.open("GET",uri,_8a?true:false);
try{
_8c.send(null);
if(_8a){
return null;
}
if(!isDocumentOk(_8c)){
var err=Error("Unable to load "+uri+" status:"+_8c.status);
err.status=_8c.status;
err.responseText=_8c.responseText;
throw err;
}
}
catch(e){
if((_8b)&&(!_8a)){
return null;
}else{
throw e;
}
}
return _8c.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_90){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_90);
}else{
try{
var _91=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_91){
_91=dojo.body();
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_90));
_91.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_90+"</div>");
}
catch(e2){
window.status=_90;
}
}
}
};
dojo.addOnLoad(function(){
dojo.hostenv._println_safe=true;
while(dojo.hostenv._println_buffer.length>0){
dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
}
});
function dj_addNodeEvtHdlr(_93,_94,fp,_96){
var _97=_93["on"+_94]||function(){
};
_93["on"+_94]=function(){
fp.apply(_93,arguments);
_97.apply(_93,arguments);
};
return true;
}
dj_addNodeEvtHdlr(window,"load",function(){
if(arguments.callee.initialized){
return;
}
arguments.callee.initialized=true;
var _98=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_98();
dojo.hostenv.modulesLoaded();
}else{
dojo.addOnLoad(_98);
}
});
dj_addNodeEvtHdlr(window,"unload",function(){
dojo.hostenv.unloaded();
});
dojo.hostenv.makeWidgets=function(){
var _99=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_99=_99.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_99=_99.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_99.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _9a=new dojo.xml.Parse();
if(_99.length>0){
for(var x=0;x<_99.length;x++){
var _9c=document.getElementById(_99[x]);
if(!_9c){
continue;
}
var _9d=_9a.parseElement(_9c,null,true);
dojo.widget.getParser().createComponents(_9d);
}
}else{
if(djConfig.parseWidgets){
var _9d=_9a.parseElement(dojo.body(),null,true);
dojo.widget.getParser().createComponents(_9d);
}
}
}
}
};
dojo.addOnLoad(function(){
if(!dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
});
try{
if(dojo.render.html.ie){
document.namespaces.add("v","urn:schemas-microsoft-com:vml");
document.createStyleSheet().addRule("v\\:*","behavior:url(#default#VML)");
}
}
catch(e){
}
dojo.hostenv.writeIncludes=function(){
};
dojo.byId=function(id,doc){
if(id&&(typeof id=="string"||id instanceof String)){
if(!doc){
doc=dojo.doc();
}
return doc.getElementById(id);
}
return id;
};
(function(){
if(typeof dj_usingBootstrap!="undefined"){
return;
}
var _a0=false;
var _a1=false;
var _a2=false;
if((typeof this["load"]=="function")&&((typeof this["Packages"]=="function")||(typeof this["Packages"]=="object"))){
_a0=true;
}else{
if(typeof this["load"]=="function"){
_a1=true;
}else{
if(window.widget){
_a2=true;
}
}
}
var _a3=[];
if((this["djConfig"])&&((djConfig["isDebug"])||(djConfig["debugAtAllCosts"]))){
_a3.push("debug.js");
}
if((this["djConfig"])&&(djConfig["debugAtAllCosts"])&&(!_a0)&&(!_a2)){
_a3.push("browser_debug.js");
}
if((this["djConfig"])&&(djConfig["compat"])){
_a3.push("compat/"+djConfig["compat"]+".js");
}
var _a4=djConfig["baseScriptUri"];
if((this["djConfig"])&&(djConfig["baseLoaderUri"])){
_a4=djConfig["baseLoaderUri"];
}
for(var x=0;x<_a3.length;x++){
var _a6=_a4+"src/"+_a3[x];
if(_a0||_a1){
load(_a6);
}else{
try{
document.write("<scr"+"ipt type='text/javascript' src='"+_a6+"'></scr"+"ipt>");
}
catch(e){
var _a7=document.createElement("script");
_a7.src=_a6;
document.getElementsByTagName("head")[0].appendChild(_a7);
}
}
}
})();
dojo.fallback_locale="en";
dojo.normalizeLocale=function(_a8){
return _a8?_a8.toLowerCase():dojo.locale;
};
dojo.requireLocalization=function(_a9,_aa,_ab){
dojo.debug("EXPERIMENTAL: dojo.requireLocalization");
var _ac=dojo.hostenv.getModuleSymbols(_a9);
var _ad=_ac.concat("nls").join("/");
_ab=dojo.normalizeLocale(_ab);
var _ae=_ab.split("-");
var _af=[];
for(var i=_ae.length;i>0;i--){
_af.push(_ae.slice(0,i).join("-"));
}
if(_af[_af.length-1]!=dojo.fallback_locale){
_af.push(dojo.fallback_locale);
}
var _b1=[_a9,"_nls",_aa].join(".");
var _b2=dojo.hostenv.startPackage(_b1);
dojo.hostenv.loaded_modules_[_b1]=_b2;
var _b3=false;
for(var i=_af.length-1;i>=0;i--){
var loc=_af[i];
var pkg=[_b1,loc].join(".");
var _b6=false;
if(!dojo.hostenv.findModule(pkg)){
dojo.hostenv.loaded_modules_[pkg]=null;
var _b7=[_ad,loc,_aa].join("/")+".js";
_b6=dojo.hostenv.loadPath(_b7,null,function(_b8){
_b2[loc]=_b8;
if(_b3){
for(var _b9 in _b3){
if(!_b2[loc][_b9]){
_b2[loc][_b9]=_b3[_b9];
}
}
}
});
}else{
_b6=true;
}
if(_b6&&_b2[loc]){
_b3=_b2[loc];
}
}
};
dojo.provide("dojo.string.common");
dojo.string.trim=function(str,wh){
if(!str.replace){
return str;
}
if(!str.length){
return str;
}
var re=(wh>0)?(/^\s+/):(wh<0)?(/\s+$/):(/^\s+|\s+$/g);
return str.replace(re,"");
};
dojo.string.trimStart=function(str){
return dojo.string.trim(str,1);
};
dojo.string.trimEnd=function(str){
return dojo.string.trim(str,-1);
};
dojo.string.repeat=function(str,_c0,_c1){
var out="";
for(var i=0;i<_c0;i++){
out+=str;
if(_c1&&i<_c0-1){
out+=_c1;
}
}
return out;
};
dojo.string.pad=function(str,len,c,dir){
var out=String(str);
if(!c){
c="0";
}
if(!dir){
dir=1;
}
while(out.length<len){
if(dir>0){
out=c+out;
}else{
out+=c;
}
}
return out;
};
dojo.string.padLeft=function(str,len,c){
return dojo.string.pad(str,len,c,1);
};
dojo.string.padRight=function(str,len,c){
return dojo.string.pad(str,len,c,-1);
};
dojo.provide("dojo.string");
dojo.provide("dojo.lang.common");
dojo.lang._mixin=function(obj,_d0){
var _d1={};
for(var x in _d0){
if(typeof _d1[x]=="undefined"||_d1[x]!=_d0[x]){
obj[x]=_d0[x];
}
}
if(dojo.render.html.ie&&dojo.lang.isFunction(_d0["toString"])&&_d0["toString"]!=obj["toString"]){
obj.toString=_d0.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_d4){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_d6,_d7){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_d6.prototype,arguments[i]);
}
return _d6;
};
dojo.lang.find=function(_d9,_da,_db,_dc){
if(!dojo.lang.isArrayLike(_d9)&&dojo.lang.isArrayLike(_da)){
dojo.deprecated("dojo.lang.find(value, array)","use dojo.lang.find(array, value) instead","0.5");
var _dd=_d9;
_d9=_da;
_da=_dd;
}
var _de=dojo.lang.isString(_d9);
if(_de){
_d9=_d9.split("");
}
if(_dc){
var _df=-1;
var i=_d9.length-1;
var end=-1;
}else{
var _df=1;
var i=0;
var end=_d9.length;
}
if(_db){
while(i!=end){
if(_d9[i]===_da){
return i;
}
i+=_df;
}
}else{
while(i!=end){
if(_d9[i]==_da){
return i;
}
i+=_df;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(_e2,_e3,_e4){
return dojo.lang.find(_e2,_e3,_e4,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(_e5,_e6){
return dojo.lang.find(_e5,_e6)>-1;
};
dojo.lang.isObject=function(it){
if(typeof it=="undefined"){
return false;
}
return (typeof it=="object"||it===null||dojo.lang.isArray(it)||dojo.lang.isFunction(it));
};
dojo.lang.isArray=function(it){
return (it instanceof Array||typeof it=="array");
};
dojo.lang.isArrayLike=function(it){
if(dojo.lang.isString(it)){
return false;
}
if(dojo.lang.isFunction(it)){
return false;
}
if(dojo.lang.isArray(it)){
return true;
}
if(typeof it!="undefined"&&it&&dojo.lang.isNumber(it.length)&&isFinite(it.length)){
return true;
}
return false;
};
dojo.lang.isFunction=function(it){
if(!it){
return false;
}
return (it instanceof Function||typeof it=="function");
};
dojo.lang.isString=function(it){
return (it instanceof String||typeof it=="string");
};
dojo.lang.isAlien=function(it){
if(!it){
return false;
}
return !dojo.lang.isFunction()&&/\{\s*\[native code\]\s*\}/.test(String(it));
};
dojo.lang.isBoolean=function(it){
return (it instanceof Boolean||typeof it=="boolean");
};
dojo.lang.isNumber=function(it){
return (it instanceof Number||typeof it=="number");
};
dojo.lang.isUndefined=function(it){
return ((it==undefined)&&(typeof it=="undefined"));
};
dojo.provide("dojo.lang.extras");
dojo.lang.setTimeout=function(_f0,_f1){
var _f2=window,argsStart=2;
if(!dojo.lang.isFunction(_f0)){
_f2=_f0;
_f0=_f1;
_f1=arguments[2];
argsStart++;
}
if(dojo.lang.isString(_f0)){
_f0=_f2[_f0];
}
var _f3=[];
for(var i=argsStart;i<arguments.length;i++){
_f3.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
_f0.apply(_f2,_f3);
},_f1);
};
dojo.lang.clearTimeout=function(_f5){
dojo.global().clearTimeout(_f5);
};
dojo.lang.getNameInObj=function(ns,_f7){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===_f7){
return new String(x);
}
}
return null;
};
dojo.lang.shallowCopy=function(obj){
var ret={},key;
for(key in obj){
if(dojo.lang.isUndefined(ret[key])){
ret[key]=obj[key];
}
}
return ret;
};
dojo.lang.firstValued=function(){
for(var i=0;i<arguments.length;i++){
if(typeof arguments[i]!="undefined"){
return arguments[i];
}
}
return undefined;
};
dojo.lang.getObjPathValue=function(_fc,_fd,_fe){
with(dojo.parseObjPath(_fc,_fd,_fe)){
return dojo.evalProp(prop,obj,_fe);
}
};
dojo.lang.setObjPathValue=function(_ff,_100,_101,_102){
if(arguments.length<4){
_102=true;
}
with(dojo.parseObjPath(_ff,_101,_102)){
if(obj&&(_102||(prop in obj))){
obj[prop]=_100;
}
}
};
dojo.provide("dojo.io.IO");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_104,_105,_106){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_104){
this.mimetype=_104;
}
if(_105){
this.transport=_105;
}
if(arguments.length>=4){
this.changeUrl=_106;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,evt){
},error:function(type,_10b){
},timeout:function(type){
},handle:function(){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_10d){
if(_10d["url"]){
_10d.url=_10d.url.toString();
}
if(_10d["formNode"]){
_10d.formNode=dojo.byId(_10d.formNode);
}
if(!_10d["method"]&&_10d["formNode"]&&_10d["formNode"].method){
_10d.method=_10d["formNode"].method;
}
if(!_10d["handle"]&&_10d["handler"]){
_10d.handle=_10d.handler;
}
if(!_10d["load"]&&_10d["loaded"]){
_10d.load=_10d.loaded;
}
if(!_10d["changeUrl"]&&_10d["changeURL"]){
_10d.changeUrl=_10d.changeURL;
}
_10d.encoding=dojo.lang.firstValued(_10d["encoding"],djConfig["bindEncoding"],"");
_10d.sendTransport=dojo.lang.firstValued(_10d["sendTransport"],djConfig["ioSendTransport"],false);
var _10e=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_10e(_10d[fn])){
continue;
}
if(_10e(_10d["handle"])){
_10d[fn]=_10d.handle;
}
}
dojo.lang.mixin(this,_10d);
}});
dojo.io.Error=function(msg,type,num){
this.message=msg;
this.type=type||"unknown";
this.number=num||0;
};
dojo.io.transports.addTransport=function(name){
this.push(name);
this[name]=dojo.io[name];
};
dojo.io.bind=function(_115){
if(!(_115 instanceof dojo.io.Request)){
try{
_115=new dojo.io.Request(_115);
}
catch(e){
dojo.debug(e);
}
}
var _116="";
if(_115["transport"]){
_116=_115["transport"];
if(!this[_116]){
return _115;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_115))){
_116=tmp;
}
}
if(_116==""){
return _115;
}
}
this[_116].bind(_115);
_115.bindSuccess=true;
return _115;
};
dojo.io.queueBind=function(_119){
if(!(_119 instanceof dojo.io.Request)){
try{
_119=new dojo.io.Request(_119);
}
catch(e){
dojo.debug(e);
}
}
var _11a=_119.load;
_119.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_11a.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _11c=_119.error;
_119.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_11c.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_119);
dojo.io._dispatchNextQueueBind();
return _119;
};
dojo.io._dispatchNextQueueBind=function(){
if(!dojo.io._queueBindInFlight){
dojo.io._queueBindInFlight=true;
if(dojo.io._bindQueue.length>0){
dojo.io.bind(dojo.io._bindQueue.shift());
}else{
dojo.io._queueBindInFlight=false;
}
}
};
dojo.io._bindQueue=[];
dojo.io._queueBindInFlight=false;
dojo.io.argsFromMap=function(map,_11f,last){
var enc=/utf/i.test(_11f||"")?encodeURIComponent:dojo.string.encodeAscii;
var _122=[];
var _123=new Object();
for(var name in map){
var _125=function(elt){
var val=enc(name)+"="+enc(elt);
_122[(last==name)?"push":"unshift"](val);
};
if(!_123[name]){
var _128=map[name];
if(dojo.lang.isArray(_128)){
dojo.lang.forEach(_128,_125);
}else{
_125(_128);
}
}
}
return _122.join("&");
};
dojo.io.setIFrameSrc=function(_129,src,_12b){
try{
var r=dojo.render.html;
if(!_12b){
if(r.safari){
_129.location=src;
}else{
frames[_129.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_129.contentWindow.document;
}else{
if(r.safari){
idoc=_129.document;
}else{
idoc=_129.contentWindow;
}
}
if(!idoc){
_129.location=src;
return;
}else{
idoc.location.replace(src);
}
}
}
catch(e){
dojo.debug(e);
dojo.debug("setIFrameSrc: "+e);
}
};
dojo.provide("dojo.lang.array");
dojo.lang.has=function(obj,name){
try{
return (typeof obj[name]!="undefined");
}
catch(e){
return false;
}
};
dojo.lang.isEmpty=function(obj){
if(dojo.lang.isObject(obj)){
var tmp={};
var _132=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_132++;
break;
}
}
return (_132==0);
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
};
dojo.lang.map=function(arr,obj,_136){
var _137=dojo.lang.isString(arr);
if(_137){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_136)){
_136=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_136){
var _138=obj;
obj=_136;
_136=_138;
}
}
if(Array.map){
var _139=Array.map(arr,_136,obj);
}else{
var _139=[];
for(var i=0;i<arr.length;++i){
_139.push(_136.call(obj,arr[i]));
}
}
if(_137){
return _139.join("");
}else{
return _139;
}
};
dojo.lang.forEach=function(_13b,_13c,_13d){
if(dojo.lang.isString(_13b)){
_13b=_13b.split("");
}
if(Array.forEach){
Array.forEach(_13b,_13c,_13d);
}else{
if(!_13d){
_13d=dj_global;
}
for(var i=0,l=_13b.length;i<l;i++){
_13c.call(_13d,_13b[i],i,_13b);
}
}
};
dojo.lang._everyOrSome=function(_13f,arr,_141,_142){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[(_13f)?"every":"some"](arr,_141,_142);
}else{
if(!_142){
_142=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _144=_141.call(_142,arr[i],i,arr);
if((_13f)&&(!_144)){
return false;
}else{
if((!_13f)&&(_144)){
return true;
}
}
}
return (_13f)?true:false;
}
};
dojo.lang.every=function(arr,_146,_147){
return this._everyOrSome(true,arr,_146,_147);
};
dojo.lang.some=function(arr,_149,_14a){
return this._everyOrSome(false,arr,_149,_14a);
};
dojo.lang.filter=function(arr,_14c,_14d){
var _14e=dojo.lang.isString(arr);
if(_14e){
arr=arr.split("");
}
if(Array.filter){
var _14f=Array.filter(arr,_14c,_14d);
}else{
if(!_14d){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_14d=dj_global;
}
var _14f=[];
for(var i=0;i<arr.length;i++){
if(_14c.call(_14d,arr[i],i,arr)){
_14f.push(arr[i]);
}
}
}
if(_14e){
return _14f.join("");
}else{
return _14f;
}
};
dojo.lang.unnest=function(){
var out=[];
for(var i=0;i<arguments.length;i++){
if(dojo.lang.isArrayLike(arguments[i])){
var add=dojo.lang.unnest.apply(this,arguments[i]);
out=out.concat(add);
}else{
out.push(arguments[i]);
}
}
return out;
};
dojo.lang.toArray=function(_154,_155){
var _156=[];
for(var i=_155||0;i<_154.length;i++){
_156.push(_154[i]);
}
return _156;
};
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_158,_159){
var fcn=dojo.lang.isString(_159)?_158[_159]:_159;
return function(){
return fcn.apply(_158,arguments);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_15b,_15c,_15d){
var nso=(_15c||dojo.lang.anon);
if((_15d)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_15b){
return x;
}
}
catch(e){
}
}
}
var ret="__"+dojo.lang.anonCtr++;
while(typeof nso[ret]!="undefined"){
ret="__"+dojo.lang.anonCtr++;
}
nso[ret]=_15b;
return ret;
};
dojo.lang.forward=function(_161){
return function(){
return this[_161].apply(this,arguments);
};
};
dojo.lang.curry=function(ns,func){
var _164=[];
ns=ns||dj_global;
if(dojo.lang.isString(func)){
func=ns[func];
}
for(var x=2;x<arguments.length;x++){
_164.push(arguments[x]);
}
var _166=(func["__preJoinArity"]||func.length)-_164.length;
function gather(_167,_168,_169){
var _16a=_169;
var _16b=_168.slice(0);
for(var x=0;x<_167.length;x++){
_16b.push(_167[x]);
}
_169=_169-_167.length;
if(_169<=0){
var res=func.apply(ns,_16b);
_169=_16a;
return res;
}else{
return function(){
return gather(arguments,_16b,_169);
};
}
}
return gather([],_164,_166);
};
dojo.lang.curryArguments=function(ns,func,args,_171){
var _172=[];
var x=_171||0;
for(x=_171;x<args.length;x++){
_172.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[ns,func].concat(_172));
};
dojo.lang.tryThese=function(){
for(var x=0;x<arguments.length;x++){
try{
if(typeof arguments[x]=="function"){
var ret=(arguments[x]());
if(ret){
return ret;
}
}
}
catch(e){
dojo.debug(e);
}
}
};
dojo.lang.delayThese=function(farr,cb,_178,_179){
if(!farr.length){
if(typeof _179=="function"){
_179();
}
return;
}
if((typeof _178=="undefined")&&(typeof cb=="number")){
_178=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_178){
_178=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_178,_179);
},_178);
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_17a,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _17a.replace(/\%\{(\w+)\}/g,function(_17d,key){
return map[key]||dojo.raise("Substitution not found: "+key);
});
};
dojo.string.capitalize=function(str){
if(!dojo.lang.isString(str)){
return "";
}
if(arguments.length==0){
str=this;
}
var _180=str.split(" ");
for(var i=0;i<_180.length;i++){
_180[i]=_180[i].charAt(0).toUpperCase()+_180[i].substring(1);
}
return _180.join(" ");
};
dojo.string.isBlank=function(str){
if(!dojo.lang.isString(str)){
return true;
}
return (dojo.string.trim(str).length==0);
};
dojo.string.encodeAscii=function(str){
if(!dojo.lang.isString(str)){
return str;
}
var ret="";
var _185=escape(str);
var _186,re=/%u([0-9A-F]{4})/i;
while((_186=_185.match(re))){
var num=Number("0x"+_186[1]);
var _188=escape("&#"+num+";");
ret+=_185.substring(0,_186.index)+_188;
_185=_185.substring(_186.index+_186[0].length);
}
ret+=_185.replace(/\+/g,"%2B");
return ret;
};
dojo.string.escape=function(type,str){
var args=dojo.lang.toArray(arguments,1);
switch(type.toLowerCase()){
case "xml":
case "html":
case "xhtml":
return dojo.string.escapeXml.apply(this,args);
case "sql":
return dojo.string.escapeSql.apply(this,args);
case "regexp":
case "regex":
return dojo.string.escapeRegExp.apply(this,args);
case "javascript":
case "jscript":
case "js":
return dojo.string.escapeJavaScript.apply(this,args);
case "ascii":
return dojo.string.encodeAscii.apply(this,args);
default:
return str;
}
};
dojo.string.escapeXml=function(str,_18d){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_18d){
str=str.replace(/'/gm,"&#39;");
}
return str;
};
dojo.string.escapeSql=function(str){
return str.replace(/'/gm,"''");
};
dojo.string.escapeRegExp=function(str){
return str.replace(/\\/gm,"\\\\").replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm,"\\$1");
};
dojo.string.escapeJavaScript=function(str){
return str.replace(/(["'\f\b\n\t\r])/gm,"\\$1");
};
dojo.string.escapeString=function(str){
return ("\""+str.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");
};
dojo.string.summary=function(str,len){
if(!len||str.length<=len){
return str;
}else{
return str.substring(0,len).replace(/\.+$/,"")+"...";
}
};
dojo.string.endsWith=function(str,end,_196){
if(_196){
str=str.toLowerCase();
end=end.toLowerCase();
}
if((str.length-end.length)<0){
return false;
}
return str.lastIndexOf(end)==str.length-end.length;
};
dojo.string.endsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.endsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.startsWith=function(str,_19a,_19b){
if(_19b){
str=str.toLowerCase();
_19a=_19a.toLowerCase();
}
return str.indexOf(_19a)==0;
};
dojo.string.startsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.startsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.has=function(str){
for(var i=1;i<arguments.length;i++){
if(str.indexOf(arguments[i])>-1){
return true;
}
}
return false;
};
dojo.string.normalizeNewlines=function(text,_1a1){
if(_1a1=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1a1=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n");
text=text.replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1a3){
var _1a4=[];
for(var i=0,prevcomma=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1a3){
_1a4.push(str.substring(prevcomma,i));
prevcomma=i+1;
}
}
_1a4.push(str.substr(prevcomma));
return _1a4;
};
dojo.provide("dojo.dom");
dojo.dom.ELEMENT_NODE=1;
dojo.dom.ATTRIBUTE_NODE=2;
dojo.dom.TEXT_NODE=3;
dojo.dom.CDATA_SECTION_NODE=4;
dojo.dom.ENTITY_REFERENCE_NODE=5;
dojo.dom.ENTITY_NODE=6;
dojo.dom.PROCESSING_INSTRUCTION_NODE=7;
dojo.dom.COMMENT_NODE=8;
dojo.dom.DOCUMENT_NODE=9;
dojo.dom.DOCUMENT_TYPE_NODE=10;
dojo.dom.DOCUMENT_FRAGMENT_NODE=11;
dojo.dom.NOTATION_NODE=12;
dojo.dom.dojoml="http://www.dojotoolkit.org/2004/dojoml";
dojo.dom.xmlns={svg:"http://www.w3.org/2000/svg",smil:"http://www.w3.org/2001/SMIL20/",mml:"http://www.w3.org/1998/Math/MathML",cml:"http://www.xml-cml.org",xlink:"http://www.w3.org/1999/xlink",xhtml:"http://www.w3.org/1999/xhtml",xul:"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",xbl:"http://www.mozilla.org/xbl",fo:"http://www.w3.org/1999/XSL/Format",xsl:"http://www.w3.org/1999/XSL/Transform",xslt:"http://www.w3.org/1999/XSL/Transform",xi:"http://www.w3.org/2001/XInclude",xforms:"http://www.w3.org/2002/01/xforms",saxon:"http://icl.com/saxon",xalan:"http://xml.apache.org/xslt",xsd:"http://www.w3.org/2001/XMLSchema",dt:"http://www.w3.org/2001/XMLSchema-datatypes",xsi:"http://www.w3.org/2001/XMLSchema-instance",rdf:"http://www.w3.org/1999/02/22-rdf-syntax-ns#",rdfs:"http://www.w3.org/2000/01/rdf-schema#",dc:"http://purl.org/dc/elements/1.1/",dcq:"http://purl.org/dc/qualifiers/1.0","soap-env":"http://schemas.xmlsoap.org/soap/envelope/",wsdl:"http://schemas.xmlsoap.org/wsdl/",AdobeExtensions:"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"};
dojo.dom.isNode=function(wh){
if(typeof Element=="object"){
try{
return wh instanceof Element;
}
catch(E){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getUniqueId=function(){
var _1a7=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_1a7.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_1a9,_1aa){
var node=_1a9.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_1aa&&node&&node.tagName&&node.tagName.toLowerCase()!=_1aa.toLowerCase()){
node=dojo.dom.nextElement(node,_1aa);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_1ac,_1ad){
var node=_1ac.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_1ad&&node&&node.tagName&&node.tagName.toLowerCase()!=_1ad.toLowerCase()){
node=dojo.dom.prevElement(node,_1ad);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_1b0){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1b0&&_1b0.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_1b0);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_1b2){
if(!node){
return null;
}
if(_1b2){
_1b2=_1b2.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1b2&&_1b2.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_1b2);
}
return node;
};
dojo.dom.moveChildren=function(_1b3,_1b4,trim){
var _1b6=0;
if(trim){
while(_1b3.hasChildNodes()&&_1b3.firstChild.nodeType==dojo.dom.TEXT_NODE){
_1b3.removeChild(_1b3.firstChild);
}
while(_1b3.hasChildNodes()&&_1b3.lastChild.nodeType==dojo.dom.TEXT_NODE){
_1b3.removeChild(_1b3.lastChild);
}
}
while(_1b3.hasChildNodes()){
_1b4.appendChild(_1b3.firstChild);
_1b6++;
}
return _1b6;
};
dojo.dom.copyChildren=function(_1b7,_1b8,trim){
var _1ba=_1b7.cloneNode(true);
return this.moveChildren(_1ba,_1b8,trim);
};
dojo.dom.removeChildren=function(node){
var _1bc=node.childNodes.length;
while(node.hasChildNodes()){
node.removeChild(node.firstChild);
}
return _1bc;
};
dojo.dom.replaceChildren=function(node,_1be){
dojo.dom.removeChildren(node);
node.appendChild(_1be);
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_1c1,_1c2){
var _1c3=[];
var _1c4=(_1c1&&(_1c1 instanceof Function||typeof _1c1=="function"));
while(node){
if(!_1c4||_1c1(node)){
_1c3.push(node);
}
if(_1c2&&_1c3.length>0){
return _1c3[0];
}
node=node.parentNode;
}
if(_1c2){
return null;
}
return _1c3;
};
dojo.dom.getAncestorsByTag=function(node,tag,_1c7){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_1c7);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_1cc,_1cd){
if(_1cd&&node){
node=node.parentNode;
}
while(node){
if(node==_1cc){
return true;
}
node=node.parentNode;
}
return false;
};
dojo.dom.innerXML=function(node){
if(node.innerXML){
return node.innerXML;
}else{
if(node.xml){
return node.xml;
}else{
if(typeof XMLSerializer!="undefined"){
return (new XMLSerializer()).serializeToString(node);
}
}
}
};
dojo.dom.createDocument=function(){
var doc=null;
var _1d0=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _1d1=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_1d1.length;i++){
try{
doc=new ActiveXObject(_1d1[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_1d0.implementation)&&(_1d0.implementation.createDocument)){
doc=_1d0.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_1d4){
if(!_1d4){
_1d4="text/xml";
}
if(!dj_undef("DOMParser")){
var _1d5=new DOMParser();
return _1d5.parseFromString(str,_1d4);
}else{
if(!dj_undef("ActiveXObject")){
var _1d6=dojo.dom.createDocument();
if(_1d6){
_1d6.async=false;
_1d6.loadXML(str);
return _1d6;
}else{
dojo.debug("toXml didn't work?");
}
}else{
_document=dojo.doc();
if(_document.createElement){
var tmp=_document.createElement("xml");
tmp.innerHTML=str;
if(_document.implementation&&_document.implementation.createDocument){
var _1d8=_document.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_1d8.importNode(tmp.childNodes.item(i),true);
}
return _1d8;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_1db){
if(_1db.firstChild){
_1db.insertBefore(node,_1db.firstChild);
}else{
_1db.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_1de){
if(_1de!=true&&(node===ref||node.nextSibling===ref)){
return false;
}
var _1df=ref.parentNode;
_1df.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_1e2){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_1e2!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_1e2);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_1e6){
if((!node)||(!ref)||(!_1e6)){
return false;
}
switch(_1e6.toLowerCase()){
case "before":
return dojo.dom.insertBefore(node,ref);
case "after":
return dojo.dom.insertAfter(node,ref);
case "first":
if(ref.firstChild){
return dojo.dom.insertBefore(node,ref.firstChild);
}else{
ref.appendChild(node);
return true;
}
break;
default:
ref.appendChild(node);
return true;
}
};
dojo.dom.insertAtIndex=function(node,_1e8,_1e9){
var _1ea=_1e8.childNodes;
if(!_1ea.length){
_1e8.appendChild(node);
return true;
}
var _1eb=null;
for(var i=0;i<_1ea.length;i++){
var _1ed=_1ea.item(i)["getAttribute"]?parseInt(_1ea.item(i).getAttribute("dojoinsertionindex")):-1;
if(_1ed<_1e9){
_1eb=_1ea.item(i);
}
}
if(_1eb){
return dojo.dom.insertAfter(node,_1eb);
}else{
return dojo.dom.insertBefore(node,_1ea.item(0));
}
};
dojo.dom.textContent=function(node,text){
if(text){
var _1f0=dojo.doc();
dojo.dom.replaceChildren(node,_1f0.createTextNode(text));
return text;
}else{
var _1f1="";
if(node==null){
return _1f1;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_1f1+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_1f1+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _1f1;
}
};
dojo.dom.hasParent=function(node){
return node&&node.parentNode&&dojo.dom.isNode(node.parentNode);
};
dojo.dom.isTag=function(node){
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName==String(arguments[i])){
return String(arguments[i]);
}
}
}
return "";
};
dojo.dom.setAttributeNS=function(elem,_1f7,_1f8,_1f9){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_1f7,_1f8,_1f9);
}else{
var _1fa=elem.ownerDocument;
var _1fb=_1fa.createNode(2,_1f8,_1f7);
_1fb.nodeValue=_1f9;
elem.setAttributeNode(_1fb);
}
};
dojo.provide("dojo.undo.browser");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:window.location.href,initialHash:window.location.hash,moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState={"url":this.initialHref,"kwArgs":args,"urlHash":this.initialHash};
},addToHistory:function(args){
var hash=null;
if(!this.historyIframe){
this.historyIframe=window.frames["djhistory"];
}
if(!this.bookmarkAnchor){
this.bookmarkAnchor=document.createElement("a");
dojo.body().appendChild(this.bookmarkAnchor);
this.bookmarkAnchor.style.display="none";
}
if((!args["changeUrl"])||(dojo.render.html.ie)){
var url=dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
}
if(args["changeUrl"]){
this.changingUrl=true;
hash="#"+((args["changeUrl"]!==true)?args["changeUrl"]:(new Date()).getTime());
setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;",1);
this.bookmarkAnchor.href=hash;
if(dojo.render.html.ie){
var _200=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_202){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_200.apply(this,[_202]);
};
if(args["back"]){
args.back=tcb;
}else{
if(args["backButton"]){
args.backButton=tcb;
}else{
if(args["handle"]){
args.handle=tcb;
}
}
}
this.forwardStack=[];
var _203=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_205){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_203){
_203.apply(this,[_205]);
}
};
if(args["forward"]){
args.forward=tfw;
}else{
if(args["forwardButton"]){
args.forwardButton=tfw;
}else{
if(args["handle"]){
args.handle=tfw;
}
}
}
}else{
if(dojo.render.html.moz){
if(!this.locationTimer){
this.locationTimer=setInterval("dojo.undo.browser.checkLocation();",200);
}
}
}
}
this.historyStack.push({"url":url,"kwArgs":args,"urlHash":hash});
},checkLocation:function(){
if(!this.changingUrl){
var hsl=this.historyStack.length;
if((window.location.hash==this.initialHash||window.location.href==this.initialHref)&&(hsl==1)){
this.handleBackButton();
return;
}
if(this.forwardStack.length>0){
if(this.forwardStack[this.forwardStack.length-1].urlHash==window.location.hash){
this.handleForwardButton();
return;
}
}
if((hsl>=2)&&(this.historyStack[hsl-2])){
if(this.historyStack[hsl-2].urlHash==window.location.hash){
this.handleBackButton();
return;
}
}
}
},iframeLoaded:function(evt,_208){
if(!dojo.render.html.opera){
var _209=this._getUrlQuery(_208.href);
if(_209==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_209==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_209==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _20a=this.historyStack.pop();
if(!_20a){
return;
}
var last=this.historyStack[this.historyStack.length-1];
if(!last&&this.historyStack.length==0){
last=this.initialState;
}
if(last){
if(last.kwArgs["back"]){
last.kwArgs["back"]();
}else{
if(last.kwArgs["backButton"]){
last.kwArgs["backButton"]();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("back");
}
}
}
}
this.forwardStack.push(_20a);
},handleForwardButton:function(){
var last=this.forwardStack.pop();
if(!last){
return;
}
if(last.kwArgs["forward"]){
last.kwArgs.forward();
}else{
if(last.kwArgs["forwardButton"]){
last.kwArgs.forwardButton();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("forward");
}
}
}
this.historyStack.push(last);
},_getUrlQuery:function(url){
var _20e=url.split("?");
if(_20e.length<2){
return null;
}else{
return _20e[1];
}
}};
dojo.provide("dojo.io.BrowserIO");
dojo.io.checkChildrenForFile=function(node){
var _210=false;
var _211=node.getElementsByTagName("input");
dojo.lang.forEach(_211,function(_212){
if(_210){
return;
}
if(_212.getAttribute("type")=="file"){
_210=true;
}
});
return _210;
};
dojo.io.formHasFile=function(_213){
return dojo.io.checkChildrenForFile(_213);
};
dojo.io.updateNode=function(node,_215){
node=dojo.byId(node);
var args=_215;
if(dojo.lang.isString(_215)){
args={url:_215};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
if(dojo["event"]){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
}
node.removeChild(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(["file","submit","image","reset","button"],type);
};
dojo.io.encodeForm=function(_21c,_21d,_21e){
if((!_21c)||(!_21c.tagName)||(!_21c.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_21e){
_21e=dojo.io.formFilter;
}
var enc=/utf/i.test(_21d||"")?encodeURIComponent:dojo.string.encodeAscii;
var _220=[];
for(var i=0;i<_21c.elements.length;i++){
var elm=_21c.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_21e(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_220.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_220.push(name+"="+enc(elm.value));
}
}else{
_220.push(name+"="+enc(elm.value));
}
}
}
var _226=_21c.getElementsByTagName("input");
for(var i=0;i<_226.length;i++){
var _227=_226[i];
if(_227.type.toLowerCase()=="image"&&_227.form==_21c&&_21e(_227)){
var name=enc(_227.name);
_220.push(name+"="+enc(_227.value));
_220.push(name+".x=0");
_220.push(name+".y=0");
}
}
return _220.join("&")+"&";
};
dojo.io.FormBind=function(args){
this.bindArgs={};
if(args&&args.formNode){
this.init(args);
}else{
if(args){
this.init({formNode:args});
}
}
};
dojo.lang.extend(dojo.io.FormBind,{form:null,bindArgs:null,clickedButton:null,init:function(args){
var form=dojo.byId(args.formNode);
if(!form||!form.tagName||form.tagName.toLowerCase()!="form"){
throw new Error("FormBind: Couldn't apply, invalid form");
}else{
if(this.form==form){
return;
}else{
if(this.form){
throw new Error("FormBind: Already applied to a form");
}
}
}
dojo.lang.mixin(this.bindArgs,args);
this.form=form;
this.connect(form,"onsubmit","submit");
for(var i=0;i<form.elements.length;i++){
var node=form.elements[i];
if(node&&node.type&&dojo.lang.inArray(["submit","button"],node.type.toLowerCase())){
this.connect(node,"onclick","click");
}
}
var _22d=form.getElementsByTagName("input");
for(var i=0;i<_22d.length;i++){
var _22e=_22d[i];
if(_22e.type.toLowerCase()=="image"&&_22e.form==form){
this.connect(_22e,"onclick","click");
}
}
},onSubmit:function(form){
return true;
},submit:function(e){
e.preventDefault();
if(this.onSubmit(this.form)){
dojo.io.bind(dojo.lang.mixin(this.bindArgs,{formFilter:dojo.lang.hitch(this,"formFilter")}));
}
},click:function(e){
var node=e.currentTarget;
if(node.disabled){
return;
}
this.clickedButton=node;
},formFilter:function(node){
var type=(node.type||"").toLowerCase();
var _235=false;
if(node.disabled||!node.name){
_235=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_235=node==this.clickedButton;
}else{
_235=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _235;
},connect:function(_236,_237,_238){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_236,_237,this,_238);
}else{
var fcn=dojo.lang.hitch(this,_238);
_236[_237]=function(e){
if(!e){
e=window.event;
}
if(!e.currentTarget){
e.currentTarget=e.srcElement;
}
if(!e.preventDefault){
e.preventDefault=function(){
window.event.returnValue=false;
};
}
fcn(e);
};
}
}});
dojo.io.XMLHTTPTransport=new function(){
var _23b=this;
var _23c={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_23e,_23f){
return url+"|"+_23e+"|"+_23f.toLowerCase();
}
function addToCache(url,_241,_242,http){
_23c[getCacheKey(url,_241,_242)]=http;
}
function getFromCache(url,_245,_246){
return _23c[getCacheKey(url,_245,_246)];
}
this.clearCache=function(){
_23c={};
};
function doLoad(_247,http,url,_24a,_24b){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_247.method.toLowerCase()=="head"){
var _24d=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _24d;
};
var _24e=_24d.split(/[\r\n]+/g);
for(var i=0;i<_24e.length;i++){
var pair=_24e[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_247.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_247.mimetype=="text/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_247.mimetype=="application/xml")||(_247.mimetype=="text/xml")){
ret=http.responseXML;
if(!ret||typeof ret=="string"||!http.getResponseHeader("Content-Type")){
ret=dojo.dom.createDocumentFromText(http.responseText);
}
}else{
ret=http.responseText;
}
}
}
}
if(_24b){
addToCache(url,_24a,_247.method,http);
}
_247[(typeof _247.load=="function")?"load":"handle"]("load",ret,http,_247);
}else{
var _251=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_247[(typeof _247.error=="function")?"error":"handle"]("error",_251,http,_247);
}
}
function setHeaders(http,_253){
if(_253["headers"]){
for(var _254 in _253["headers"]){
if(_254.toLowerCase()=="content-type"&&!_253["contentType"]){
_253["contentType"]=_253["headers"][_254];
}else{
http.setRequestHeader(_254,_253["headers"][_254]);
}
}
}
}
this.inFlight=[];
this.inFlightTimer=null;
this.startWatchingInFlight=function(){
if(!this.inFlightTimer){
this.inFlightTimer=setInterval("dojo.io.XMLHTTPTransport.watchInFlight();",10);
}
};
this.watchInFlight=function(){
var now=null;
for(var x=this.inFlight.length-1;x>=0;x--){
var tif=this.inFlight[x];
if(!tif){
this.inFlight.splice(x,1);
continue;
}
if(4==tif.http.readyState){
this.inFlight.splice(x,1);
doLoad(tif.req,tif.http,tif.url,tif.query,tif.useCache);
}else{
if(tif.startTime){
if(!now){
now=(new Date()).getTime();
}
if(tif.startTime+(tif.req.timeoutSeconds*1000)<now){
if(typeof tif.http.abort=="function"){
tif.http.abort();
}
this.inFlight.splice(x,1);
tif.req[(typeof tif.req.timeout=="function")?"timeout":"handle"]("timeout",null,tif.http,tif.req);
}
}
}
}
if(this.inFlight.length==0){
clearInterval(this.inFlightTimer);
this.inFlightTimer=null;
}
};
var _258=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_259){
return _258&&dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript","text/json"],(_259["mimetype"].toLowerCase()||""))&&!(_259["formNode"]&&dojo.io.formHasFile(_259["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_25a){
if(!_25a["url"]){
if(!_25a["formNode"]&&(_25a["backButton"]||_25a["back"]||_25a["changeUrl"]||_25a["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_25a);
return true;
}
}
var url=_25a.url;
var _25c="";
if(_25a["formNode"]){
var ta=_25a.formNode.getAttribute("action");
if((ta)&&(!_25a["url"])){
url=ta;
}
var tp=_25a.formNode.getAttribute("method");
if((tp)&&(!_25a["method"])){
_25a.method=tp;
}
_25c+=dojo.io.encodeForm(_25a.formNode,_25a.encoding,_25a["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_25a["file"]){
_25a.method="post";
}
if(!_25a["method"]){
_25a.method="get";
}
if(_25a.method.toLowerCase()=="get"){
_25a.multipart=false;
}else{
if(_25a["file"]){
_25a.multipart=true;
}else{
if(!_25a["multipart"]){
_25a.multipart=false;
}
}
}
if(_25a["backButton"]||_25a["back"]||_25a["changeUrl"]){
dojo.undo.browser.addToHistory(_25a);
}
var _25f=_25a["content"]||{};
if(_25a.sendTransport){
_25f["dojo.transport"]="xmlhttp";
}
do{
if(_25a.postContent){
_25c=_25a.postContent;
break;
}
if(_25f){
_25c+=dojo.io.argsFromMap(_25f,_25a.encoding);
}
if(_25a.method.toLowerCase()=="get"||!_25a.multipart){
break;
}
var t=[];
if(_25c.length){
var q=_25c.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_25a.file){
if(dojo.lang.isArray(_25a.file)){
for(var i=0;i<_25a.file.length;++i){
var o=_25a.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_25a.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_25c=t.join("\r\n");
}
}while(false);
var _265=_25a["sync"]?false:true;
var _266=_25a["preventCache"]||(this.preventCache==true&&_25a["preventCache"]!=false);
var _267=_25a["useCache"]==true||(this.useCache==true&&_25a["useCache"]!=false);
if(!_266&&_267){
var _268=getFromCache(url,_25c,_25a.method);
if(_268){
doLoad(_25a,_268,url,_25c,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_25a);
var _26a=false;
if(_265){
var _26b=this.inFlight.push({"req":_25a,"http":http,"url":url,"query":_25c,"useCache":_267,"startTime":_25a.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}
if(_25a.method.toLowerCase()=="post"){
http.open("POST",url,_265);
setHeaders(http,_25a);
http.setRequestHeader("Content-Type",_25a.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_25a.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_25c);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_25a,{status:404},url,_25c,_267);
}
}else{
var _26c=url;
if(_25c!=""){
_26c+=(_26c.indexOf("?")>-1?"&":"?")+_25c;
}
if(_266){
_26c+=(dojo.string.endsWithAny(_26c,"?","&")?"":(_26c.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
http.open(_25a.method.toUpperCase(),_26c,_265);
setHeaders(http,_25a);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_25a,{status:404},url,_25c,_267);
}
}
if(!_265){
doLoad(_25a,http,url,_25c,_267);
}
_25a.abort=function(){
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};

