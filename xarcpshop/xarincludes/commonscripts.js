var popUpWin = false;
var browsertype=false;
if ((navigator.appName=="Netscape")&&(parseInt(navigator.appVersion)>=3)) browsertype=true;
if ((navigator.appName=="Microsoft Internet Explorer")&&(parseInt(navigator.appVersion)>=4)) browsertype=true;

function e (z, h, w, b, g) {
    //document.write('<div style="width:'+w+';height:'+h+';background:white url(http://zoom.cafepress.com/'+(z%10)+'/'+z+'_zoom.jpg) no-repeat center center;"><img border="'+b+'" class="imageborder" src="http://www.cafepress.com/cp/img/'+(g?'zoom':'spacer')+'.gif" width="'+w+'" height="'+h+'"></div>');
    document.write('<div style="width:'+w/2+';height:'+h/2+';background:white url(http://zoom.cafepress.com/'+(z%10)+'/'+z+'_zoom.jpg) no-repeat center center;"><img border="'+b+'" class="imageborder" src="http://www.cafepress.com/cp/img/spacer.gif" width="'+w/2+'" height="'+h/2+'"></div>');
}

function objectdata(hsize,vsize,hilite,original,messge)
{if (browsertype)
{        this.messge=messge;
        this.simg=new Image(hsize,vsize);
        this.simg.src=hilite;
        this.rimg=new Image(hsize,vsize);
        this.rimg.src=original;
}}

function swapimg(ToImg, FromImg)
  {
    document.images[ToImg].src=document.images[FromImg].src
  }

function hilite(name)
{if (browsertype)
{//window.status=object[name].messge;
document[name].src=object[name].simg.src;}}

function original(name)
{if (browsertype)
{//window.status="";
document[name].src=object[name].rimg.src;}}

function launchHelp(newURL, newFeatures)
{
  if ((navigator.appName=='Microsoft Internet Explorer') && (window.HelpWindow)) HelpWindow.close();
  HelpWindow = open(newURL, "HelpWindow", newFeatures + ",screenX=0,left=0,screenY=0,top=0,channelmode=0,dependent=0,directories=0,fullscreen=0,location=0,menubar=0,resizable=0,status=0,toolbar=0,scroll=1");
  if (HelpWindow.opener == null) HelpWindow.opener = window;
  HelpWindow.focus();
}

function CheckPopup() {
    if (popUpWin) {
        popUpWin.close();
    };
};


function launchWin(href, target, params)
{
  var features = params + ',screenX=0,left=0,screenY=0,top=0,channelmode=0,dependent=0,directories=0,fullscreen=0,location=0,menubar=0,resizable=1,status=0,toolbar=0';
  popWin = window.open(href, target, features);
  if (popWin.focus) popWin.focus();
}
