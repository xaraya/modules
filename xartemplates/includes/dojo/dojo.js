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
dojo.locale=djConfig.locale;
dojo.version={major:0,minor:3,patch:1,flag:"svn-4896",revision:Number("$Rev: 4883 $".match(/[0-9]+/)[0]),toString:function(){
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
dojo.locale=dojo.locale||(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
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
(function(){
var _ba=djConfig.extraLocale;
if(_ba){
var req=dojo.requireLocalization;
dojo.requireLocalization=function(m,b,_be){
req(m,b,_be);
if(_be){
return;
}
if(djConfig.extraLocale instanceof Array){
for(var i=0;i<_ba.length;i++){
req(m,b,_ba[i]);
}
}else{
req(m,b,_ba);
}
};
}
})();
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
dojo.string.repeat=function(str,_c6,_c7){
var out="";
for(var i=0;i<_c6;i++){
out+=str;
if(_c7&&i<_c6-1){
out+=_c7;
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
dojo.lang._mixin=function(obj,_d6){
var _d7={};
for(var x in _d6){
if(typeof _d7[x]=="undefined"||_d7[x]!=_d6[x]){
obj[x]=_d6[x];
}
}
if(dojo.render.html.ie&&dojo.lang.isFunction(_d6["toString"])&&_d6["toString"]!=obj["toString"]){
obj.toString=_d6.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_da){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_dc,_dd){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_dc.prototype,arguments[i]);
}
return _dc;
};
dojo.lang.find=function(_df,_e0,_e1,_e2){
if(!dojo.lang.isArrayLike(_df)&&dojo.lang.isArrayLike(_e0)){
dojo.deprecated("dojo.lang.find(value, array)","use dojo.lang.find(array, value) instead","0.5");
var _e3=_df;
_df=_e0;
_e0=_e3;
}
var _e4=dojo.lang.isString(_df);
if(_e4){
_df=_df.split("");
}
if(_e2){
var _e5=-1;
var i=_df.length-1;
var end=-1;
}else{
var _e5=1;
var i=0;
var end=_df.length;
}
if(_e1){
while(i!=end){
if(_df[i]===_e0){
return i;
}
i+=_e5;
}
}else{
while(i!=end){
if(_df[i]==_e0){
return i;
}
i+=_e5;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(_e8,_e9,_ea){
return dojo.lang.find(_e8,_e9,_ea,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(_eb,_ec){
return dojo.lang.find(_eb,_ec)>-1;
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
dojo.lang.setTimeout=function(_f6,_f7){
var _f8=window,argsStart=2;
if(!dojo.lang.isFunction(_f6)){
_f8=_f6;
_f6=_f7;
_f7=arguments[2];
argsStart++;
}
if(dojo.lang.isString(_f6)){
_f6=_f8[_f6];
}
var _f9=[];
for(var i=argsStart;i<arguments.length;i++){
_f9.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
_f6.apply(_f8,_f9);
},_f7);
};
dojo.lang.clearTimeout=function(_fb){
dojo.global().clearTimeout(_fb);
};
dojo.lang.getNameInObj=function(ns,_fd){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===_fd){
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
dojo.lang.getObjPathValue=function(_102,_103,_104){
with(dojo.parseObjPath(_102,_103,_104)){
return dojo.evalProp(prop,obj,_104);
}
};
dojo.lang.setObjPathValue=function(_105,_106,_107,_108){
if(arguments.length<4){
_108=true;
}
with(dojo.parseObjPath(_105,_107,_108)){
if(obj&&(_108||(prop in obj))){
obj[prop]=_106;
}
}
};
dojo.provide("dojo.io.IO");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_10a,_10b,_10c){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_10a){
this.mimetype=_10a;
}
if(_10b){
this.transport=_10b;
}
if(arguments.length>=4){
this.changeUrl=_10c;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,evt){
},error:function(type,_111){
},timeout:function(type){
},handle:function(){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_113){
if(_113["url"]){
_113.url=_113.url.toString();
}
if(_113["formNode"]){
_113.formNode=dojo.byId(_113.formNode);
}
if(!_113["method"]&&_113["formNode"]&&_113["formNode"].method){
_113.method=_113["formNode"].method;
}
if(!_113["handle"]&&_113["handler"]){
_113.handle=_113.handler;
}
if(!_113["load"]&&_113["loaded"]){
_113.load=_113.loaded;
}
if(!_113["changeUrl"]&&_113["changeURL"]){
_113.changeUrl=_113.changeURL;
}
_113.encoding=dojo.lang.firstValued(_113["encoding"],djConfig["bindEncoding"],"");
_113.sendTransport=dojo.lang.firstValued(_113["sendTransport"],djConfig["ioSendTransport"],false);
var _114=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_113[fn]&&_114(_113[fn])){
continue;
}
if(_113["handle"]&&_114(_113["handle"])){
_113[fn]=_113.handle;
}
}
dojo.lang.mixin(this,_113);
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
dojo.io.bind=function(_11b){
if(!(_11b instanceof dojo.io.Request)){
try{
_11b=new dojo.io.Request(_11b);
}
catch(e){
dojo.debug(e);
}
}
var _11c="";
if(_11b["transport"]){
_11c=_11b["transport"];
if(!this[_11c]){
return _11b;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_11b))){
_11c=tmp;
}
}
if(_11c==""){
return _11b;
}
}
this[_11c].bind(_11b);
_11b.bindSuccess=true;
return _11b;
};
dojo.io.queueBind=function(_11f){
if(!(_11f instanceof dojo.io.Request)){
try{
_11f=new dojo.io.Request(_11f);
}
catch(e){
dojo.debug(e);
}
}
var _120=_11f.load;
_11f.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_120.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _122=_11f.error;
_11f.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_122.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_11f);
dojo.io._dispatchNextQueueBind();
return _11f;
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
dojo.io.argsFromMap=function(map,_125,last){
var enc=/utf/i.test(_125||"")?encodeURIComponent:dojo.string.encodeAscii;
var _128=[];
var _129=new Object();
for(var name in map){
var _12b=function(elt){
var val=enc(name)+"="+enc(elt);
_128[(last==name)?"push":"unshift"](val);
};
if(!_129[name]){
var _12e=map[name];
if(dojo.lang.isArray(_12e)){
dojo.lang.forEach(_12e,_12b);
}else{
_12b(_12e);
}
}
}
return _128.join("&");
};
dojo.io.setIFrameSrc=function(_12f,src,_131){
try{
var r=dojo.render.html;
if(!_131){
if(r.safari){
_12f.location=src;
}else{
frames[_12f.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_12f.contentWindow.document;
}else{
if(r.safari){
idoc=_12f.document;
}else{
idoc=_12f.contentWindow;
}
}
if(!idoc){
_12f.location=src;
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
var _138=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_138++;
break;
}
}
return (_138==0);
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
};
dojo.lang.map=function(arr,obj,_13c){
var _13d=dojo.lang.isString(arr);
if(_13d){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_13c)){
_13c=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_13c){
var _13e=obj;
obj=_13c;
_13c=_13e;
}
}
if(Array.map){
var _13f=Array.map(arr,_13c,obj);
}else{
var _13f=[];
for(var i=0;i<arr.length;++i){
_13f.push(_13c.call(obj,arr[i]));
}
}
if(_13d){
return _13f.join("");
}else{
return _13f;
}
};
dojo.lang.forEach=function(_141,_142,_143){
if(dojo.lang.isString(_141)){
_141=_141.split("");
}
if(Array.forEach){
Array.forEach(_141,_142,_143);
}else{
if(!_143){
_143=dj_global;
}
for(var i=0,l=_141.length;i<l;i++){
_142.call(_143,_141[i],i,_141);
}
}
};
dojo.lang._everyOrSome=function(_145,arr,_147,_148){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[(_145)?"every":"some"](arr,_147,_148);
}else{
if(!_148){
_148=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _14a=_147.call(_148,arr[i],i,arr);
if((_145)&&(!_14a)){
return false;
}else{
if((!_145)&&(_14a)){
return true;
}
}
}
return (_145)?true:false;
}
};
dojo.lang.every=function(arr,_14c,_14d){
return this._everyOrSome(true,arr,_14c,_14d);
};
dojo.lang.some=function(arr,_14f,_150){
return this._everyOrSome(false,arr,_14f,_150);
};
dojo.lang.filter=function(arr,_152,_153){
var _154=dojo.lang.isString(arr);
if(_154){
arr=arr.split("");
}
if(Array.filter){
var _155=Array.filter(arr,_152,_153);
}else{
if(!_153){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_153=dj_global;
}
var _155=[];
for(var i=0;i<arr.length;i++){
if(_152.call(_153,arr[i],i,arr)){
_155.push(arr[i]);
}
}
}
if(_154){
return _155.join("");
}else{
return _155;
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
dojo.lang.toArray=function(_15a,_15b){
var _15c=[];
for(var i=_15b||0;i<_15a.length;i++){
_15c.push(_15a[i]);
}
return _15c;
};
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_15e,_15f){
var fcn=dojo.lang.isString(_15f)?_15e[_15f]:_15f;
return function(){
return fcn.apply(_15e,arguments);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_161,_162,_163){
var nso=(_162||dojo.lang.anon);
if((_163)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_161){
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
nso[ret]=_161;
return ret;
};
dojo.lang.forward=function(_167){
return function(){
return this[_167].apply(this,arguments);
};
};
dojo.lang.curry=function(ns,func){
var _16a=[];
ns=ns||dj_global;
if(dojo.lang.isString(func)){
func=ns[func];
}
for(var x=2;x<arguments.length;x++){
_16a.push(arguments[x]);
}
var _16c=(func["__preJoinArity"]||func.length)-_16a.length;
function gather(_16d,_16e,_16f){
var _170=_16f;
var _171=_16e.slice(0);
for(var x=0;x<_16d.length;x++){
_171.push(_16d[x]);
}
_16f=_16f-_16d.length;
if(_16f<=0){
var res=func.apply(ns,_171);
_16f=_170;
return res;
}else{
return function(){
return gather(arguments,_171,_16f);
};
}
}
return gather([],_16a,_16c);
};
dojo.lang.curryArguments=function(ns,func,args,_177){
var _178=[];
var x=_177||0;
for(x=_177;x<args.length;x++){
_178.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[ns,func].concat(_178));
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
dojo.lang.delayThese=function(farr,cb,_17e,_17f){
if(!farr.length){
if(typeof _17f=="function"){
_17f();
}
return;
}
if((typeof _17e=="undefined")&&(typeof cb=="number")){
_17e=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_17e){
_17e=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_17e,_17f);
},_17e);
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_180,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _180.replace(/\%\{(\w+)\}/g,function(_183,key){
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
var _186=str.split(" ");
for(var i=0;i<_186.length;i++){
_186[i]=_186[i].charAt(0).toUpperCase()+_186[i].substring(1);
}
return _186.join(" ");
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
var _18b=escape(str);
var _18c,re=/%u([0-9A-F]{4})/i;
while((_18c=_18b.match(re))){
var num=Number("0x"+_18c[1]);
var _18e=escape("&#"+num+";");
ret+=_18b.substring(0,_18c.index)+_18e;
_18b=_18b.substring(_18c.index+_18c[0].length);
}
ret+=_18b.replace(/\+/g,"%2B");
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
dojo.string.escapeXml=function(str,_193){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_193){
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
dojo.string.endsWith=function(str,end,_19c){
if(_19c){
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
dojo.string.startsWith=function(str,_1a0,_1a1){
if(_1a1){
str=str.toLowerCase();
_1a0=_1a0.toLowerCase();
}
return str.indexOf(_1a0)==0;
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
dojo.string.normalizeNewlines=function(text,_1a7){
if(_1a7=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1a7=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n");
text=text.replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1a9){
var _1aa=[];
for(var i=0,prevcomma=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1a9){
_1aa.push(str.substring(prevcomma,i));
prevcomma=i+1;
}
}
_1aa.push(str.substr(prevcomma));
return _1aa;
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
var _1ad=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_1ad.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_1af,_1b0){
var node=_1af.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_1b0&&node&&node.tagName&&node.tagName.toLowerCase()!=_1b0.toLowerCase()){
node=dojo.dom.nextElement(node,_1b0);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_1b2,_1b3){
var node=_1b2.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_1b3&&node&&node.tagName&&node.tagName.toLowerCase()!=_1b3.toLowerCase()){
node=dojo.dom.prevElement(node,_1b3);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_1b6){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1b6&&_1b6.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_1b6);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_1b8){
if(!node){
return null;
}
if(_1b8){
_1b8=_1b8.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1b8&&_1b8.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_1b8);
}
return node;
};
dojo.dom.moveChildren=function(_1b9,_1ba,trim){
var _1bc=0;
if(trim){
while(_1b9.hasChildNodes()&&_1b9.firstChild.nodeType==dojo.dom.TEXT_NODE){
_1b9.removeChild(_1b9.firstChild);
}
while(_1b9.hasChildNodes()&&_1b9.lastChild.nodeType==dojo.dom.TEXT_NODE){
_1b9.removeChild(_1b9.lastChild);
}
}
while(_1b9.hasChildNodes()){
_1ba.appendChild(_1b9.firstChild);
_1bc++;
}
return _1bc;
};
dojo.dom.copyChildren=function(_1bd,_1be,trim){
var _1c0=_1bd.cloneNode(true);
return this.moveChildren(_1c0,_1be,trim);
};
dojo.dom.removeChildren=function(node){
var _1c2=node.childNodes.length;
while(node.hasChildNodes()){
node.removeChild(node.firstChild);
}
return _1c2;
};
dojo.dom.replaceChildren=function(node,_1c4){
dojo.dom.removeChildren(node);
node.appendChild(_1c4);
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_1c7,_1c8){
var _1c9=[];
var _1ca=(_1c7&&(_1c7 instanceof Function||typeof _1c7=="function"));
while(node){
if(!_1ca||_1c7(node)){
_1c9.push(node);
}
if(_1c8&&_1c9.length>0){
return _1c9[0];
}
node=node.parentNode;
}
if(_1c8){
return null;
}
return _1c9;
};
dojo.dom.getAncestorsByTag=function(node,tag,_1cd){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_1cd);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_1d2,_1d3){
if(_1d3&&node){
node=node.parentNode;
}
while(node){
if(node==_1d2){
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
var _1d6=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _1d7=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_1d7.length;i++){
try{
doc=new ActiveXObject(_1d7[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_1d6.implementation)&&(_1d6.implementation.createDocument)){
doc=_1d6.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_1da){
if(!_1da){
_1da="text/xml";
}
if(!dj_undef("DOMParser")){
var _1db=new DOMParser();
return _1db.parseFromString(str,_1da);
}else{
if(!dj_undef("ActiveXObject")){
var _1dc=dojo.dom.createDocument();
if(_1dc){
_1dc.async=false;
_1dc.loadXML(str);
return _1dc;
}else{
dojo.debug("toXml didn't work?");
}
}else{
_document=dojo.doc();
if(_document.createElement){
var tmp=_document.createElement("xml");
tmp.innerHTML=str;
if(_document.implementation&&_document.implementation.createDocument){
var _1de=_document.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_1de.importNode(tmp.childNodes.item(i),true);
}
return _1de;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_1e1){
if(_1e1.firstChild){
_1e1.insertBefore(node,_1e1.firstChild);
}else{
_1e1.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_1e4){
if(_1e4!=true&&(node===ref||node.nextSibling===ref)){
return false;
}
var _1e5=ref.parentNode;
_1e5.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_1e8){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_1e8!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_1e8);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_1ec){
if((!node)||(!ref)||(!_1ec)){
return false;
}
switch(_1ec.toLowerCase()){
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
dojo.dom.insertAtIndex=function(node,_1ee,_1ef){
var _1f0=_1ee.childNodes;
if(!_1f0.length){
_1ee.appendChild(node);
return true;
}
var _1f1=null;
for(var i=0;i<_1f0.length;i++){
var _1f3=_1f0.item(i)["getAttribute"]?parseInt(_1f0.item(i).getAttribute("dojoinsertionindex")):-1;
if(_1f3<_1ef){
_1f1=_1f0.item(i);
}
}
if(_1f1){
return dojo.dom.insertAfter(node,_1f1);
}else{
return dojo.dom.insertBefore(node,_1f0.item(0));
}
};
dojo.dom.textContent=function(node,text){
if(text){
var _1f6=dojo.doc();
dojo.dom.replaceChildren(node,_1f6.createTextNode(text));
return text;
}else{
var _1f7="";
if(node==null){
return _1f7;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_1f7+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_1f7+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _1f7;
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
dojo.dom.setAttributeNS=function(elem,_1fd,_1fe,_1ff){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_1fd,_1fe,_1ff);
}else{
var _200=elem.ownerDocument;
var _201=_200.createNode(2,_1fe,_1fd);
_201.nodeValue=_1ff;
elem.setAttributeNode(_201);
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
var _206=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_208){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_206.apply(this,[_208]);
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
var _209=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_20b){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_209){
_209.apply(this,[_20b]);
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
},iframeLoaded:function(evt,_20e){
if(!dojo.render.html.opera){
var _20f=this._getUrlQuery(_20e.href);
if(_20f==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_20f==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_20f==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _210=this.historyStack.pop();
if(!_210){
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
this.forwardStack.push(_210);
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
var _214=url.split("?");
if(_214.length<2){
return null;
}else{
return _214[1];
}
}};
dojo.provide("dojo.io.BrowserIO");
dojo.io.checkChildrenForFile=function(node){
var _216=false;
var _217=node.getElementsByTagName("input");
dojo.lang.forEach(_217,function(_218){
if(_216){
return;
}
if(_218.getAttribute("type")=="file"){
_216=true;
}
});
return _216;
};
dojo.io.formHasFile=function(_219){
return dojo.io.checkChildrenForFile(_219);
};
dojo.io.updateNode=function(node,_21b){
node=dojo.byId(node);
var args=_21b;
if(dojo.lang.isString(_21b)){
args={url:_21b};
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
dojo.io.encodeForm=function(_222,_223,_224){
if((!_222)||(!_222.tagName)||(!_222.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_224){
_224=dojo.io.formFilter;
}
var enc=/utf/i.test(_223||"")?encodeURIComponent:dojo.string.encodeAscii;
var _226=[];
for(var i=0;i<_222.elements.length;i++){
var elm=_222.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_224(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_226.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_226.push(name+"="+enc(elm.value));
}
}else{
_226.push(name+"="+enc(elm.value));
}
}
}
var _22c=_222.getElementsByTagName("input");
for(var i=0;i<_22c.length;i++){
var _22d=_22c[i];
if(_22d.type.toLowerCase()=="image"&&_22d.form==_222&&_224(_22d)){
var name=enc(_22d.name);
_226.push(name+"="+enc(_22d.value));
_226.push(name+".x=0");
_226.push(name+".y=0");
}
}
return _226.join("&")+"&";
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
var _233=form.getElementsByTagName("input");
for(var i=0;i<_233.length;i++){
var _234=_233[i];
if(_234.type.toLowerCase()=="image"&&_234.form==form){
this.connect(_234,"onclick","click");
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
var _23b=false;
if(node.disabled||!node.name){
_23b=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_23b=node==this.clickedButton;
}else{
_23b=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _23b;
},connect:function(_23c,_23d,_23e){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_23c,_23d,this,_23e);
}else{
var fcn=dojo.lang.hitch(this,_23e);
_23c[_23d]=function(e){
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
var _241=this;
var _242={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_244,_245){
return url+"|"+_244+"|"+_245.toLowerCase();
}
function addToCache(url,_247,_248,http){
_242[getCacheKey(url,_247,_248)]=http;
}
function getFromCache(url,_24b,_24c){
return _242[getCacheKey(url,_24b,_24c)];
}
this.clearCache=function(){
_242={};
};
function doLoad(_24d,http,url,_250,_251){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_24d.method.toLowerCase()=="head"){
var _253=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _253;
};
var _254=_253.split(/[\r\n]+/g);
for(var i=0;i<_254.length;i++){
var pair=_254[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_24d.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_24d.mimetype=="text/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_24d.mimetype=="application/xml")||(_24d.mimetype=="text/xml")){
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
if(_251){
addToCache(url,_250,_24d.method,http);
}
_24d[(typeof _24d.load=="function")?"load":"handle"]("load",ret,http,_24d);
}else{
var _257=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_24d[(typeof _24d.error=="function")?"error":"handle"]("error",_257,http,_24d);
}
}
function setHeaders(http,_259){
if(_259["headers"]){
for(var _25a in _259["headers"]){
if(_25a.toLowerCase()=="content-type"&&!_259["contentType"]){
_259["contentType"]=_259["headers"][_25a];
}else{
http.setRequestHeader(_25a,_259["headers"][_25a]);
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
var _25e=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_25f){
return _25e&&dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript","text/json"],(_25f["mimetype"].toLowerCase()||""))&&!(_25f["formNode"]&&dojo.io.formHasFile(_25f["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_260){
if(!_260["url"]){
if(!_260["formNode"]&&(_260["backButton"]||_260["back"]||_260["changeUrl"]||_260["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_260);
return true;
}
}
var url=_260.url;
var _262="";
if(_260["formNode"]){
var ta=_260.formNode.getAttribute("action");
if((ta)&&(!_260["url"])){
url=ta;
}
var tp=_260.formNode.getAttribute("method");
if((tp)&&(!_260["method"])){
_260.method=tp;
}
_262+=dojo.io.encodeForm(_260.formNode,_260.encoding,_260["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_260["file"]){
_260.method="post";
}
if(!_260["method"]){
_260.method="get";
}
if(_260.method.toLowerCase()=="get"){
_260.multipart=false;
}else{
if(_260["file"]){
_260.multipart=true;
}else{
if(!_260["multipart"]){
_260.multipart=false;
}
}
}
if(_260["backButton"]||_260["back"]||_260["changeUrl"]){
dojo.undo.browser.addToHistory(_260);
}
var _265=_260["content"]||{};
if(_260.sendTransport){
_265["dojo.transport"]="xmlhttp";
}
do{
if(_260.postContent){
_262=_260.postContent;
break;
}
if(_265){
_262+=dojo.io.argsFromMap(_265,_260.encoding);
}
if(_260.method.toLowerCase()=="get"||!_260.multipart){
break;
}
var t=[];
if(_262.length){
var q=_262.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_260.file){
if(dojo.lang.isArray(_260.file)){
for(var i=0;i<_260.file.length;++i){
var o=_260.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_260.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_262=t.join("\r\n");
}
}while(false);
var _26b=_260["sync"]?false:true;
var _26c=_260["preventCache"]||(this.preventCache==true&&_260["preventCache"]!=false);
var _26d=_260["useCache"]==true||(this.useCache==true&&_260["useCache"]!=false);
if(!_26c&&_26d){
var _26e=getFromCache(url,_262,_260.method);
if(_26e){
doLoad(_260,_26e,url,_262,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_260);
var _270=false;
if(_26b){
var _271=this.inFlight.push({"req":_260,"http":http,"url":url,"query":_262,"useCache":_26d,"startTime":_260.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}
if(_260.method.toLowerCase()=="post"){
http.open("POST",url,_26b);
setHeaders(http,_260);
http.setRequestHeader("Content-Type",_260.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_260.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_262);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_260,{status:404},url,_262,_26d);
}
}else{
var _272=url;
if(_262!=""){
_272+=(_272.indexOf("?")>-1?"&":"?")+_262;
}
if(_26c){
_272+=(dojo.string.endsWithAny(_272,"?","&")?"":(_272.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
http.open(_260.method.toUpperCase(),_272,_26b);
setHeaders(http,_260);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_260,{status:404},url,_262,_26d);
}
}
if(!_26b){
doLoad(_260,http,url,_262,_26d);
}
_260.abort=function(){
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};

