/*
 * 15.09.2007 Jason Judge:
 * - moved global functions into jQuery object
 * - fix to same-height for IE
 */
/* Bugfixing Olaf Bosch http://olaf-bosch.de/ same-height works correct
 * Demo @ http://olaf-bosch.de/bugs/jquery/nifty/index.html
 * 07.01.2007
 ***********************************************************************
 * Nifty for jQuery is a modified and optimized version of Nifty Corners Cube.
 * The new one has been programmed by Paul Bakaus (paul.bakaus@gmail.com), read below
 * for further copyright information.
 */

/* Nifty Corners Cube - rounded corners with CSS and Javascript
Copyright 2006 Alessandro Fulciniti (a.fulciniti@html.it)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* JDJ 2007-09-15: stylesheet is loaded directly by Xaraya
jQuery(document).ready(function() {
	var l= document.createElement("link");
	jQuery(l).attr("type","text/css");
	jQuery(l).attr("rel","stylesheet");
	jQuery(l).attr("href","nifty.css");
	jQuery(l).attr("media","screen");
	jQuery("head").append(l);	
});
*/

/* 'this' is an array of selected blocks */

(function(jQuery) {
	jQuery.fn.nifty = function(options){
		if((document.createElement && Array.prototype.push) == false) return;
		
		options = options || "";
		h = (options.indexOf("fixed-height") >= 0) ? this.offsetHeight : 0;

		/* Add curves to each block */
		this.each(function(){ 
			var i,top="",bottom="";
			if(options != ""){
				options=options.replace("left","tl bl");
				options=options.replace("right","tr br");
				options=options.replace("top","tr tl");
				options=options.replace("bottom","br bl");
				options=options.replace("transparent","alias");
				if(options.indexOf("tl") >= 0) { top="both"; if(options.indexOf("tr") == -1) top="left"; } else if(options.indexOf("tr") >= 0) top="right";
				if(options.indexOf("bl") >= 0) { bottom="both"; if(options.indexOf("br") == -1) bottom="left"; } else if(options.indexOf("br") >= 0) bottom="right";
			}
			if(top=="" && bottom=="" && options.indexOf("none") == -1){top="both";bottom="both";}
			
			// IE Fix
			if(this.currentStyle!=null && this.currentStyle.hasLayout!=null && this.currentStyle.hasLayout==false)
				jQuery(this).css("display","inline-block");

			if(top!="") {
				//add top
				// TODO: create elements only through jQuery
				var d=document.createElement("b"),lim=4,border="",p,i,btype="r",bk,color;
				jQuery(d).css("marginLeft","-"+jQuery.fn.nifty._niftyGP(this,"Left")+"px");
				jQuery(d).css("marginRight","-"+jQuery.fn.nifty._niftyGP(this,"Right")+"px");
				if(options.indexOf("alias") >= 0 || (color=jQuery.fn.nifty._niftyBC(this))=="transparent"){
					color="transparent";bk="transparent"; border=jQuery.fn.nifty._niftyPBC(this);btype="t";
					}
				else{
					bk=jQuery.fn.nifty._niftyPBC(this); border=jQuery.fn.nifty._niftyMix(color,bk);
					}
				jQuery(d).css("background",bk);
				d.className="niftycorners";
				p=jQuery.fn.nifty._niftyGP(this,"Top");
				if(options.indexOf("small") >= 0){
					jQuery(d).css("marginBottom",(p-2)+"px");
					btype+="s"; lim=2;
					}
				else if(options.indexOf("big") >= 0){
					jQuery(d).css("marginBottom",(p-10)+"px");
					btype+="b"; lim=8;
					}
				else jQuery(d).css("marginBottom",(p-5)+"px");
				for(i=1;i<=lim;i++)
					jQuery(d).append(jQuery.fn.nifty.CreateStrip(i,top,color,border,btype));
				jQuery(this).css("paddingTop", "0px");
				jQuery(this).prepend(d);				
			}
			if(bottom!="") {
				//add bottom
				// TODO: create elements only through jQuery
				var d=document.createElement("b"),lim=4,border="",p,i,btype="r",bk,color;
				jQuery(d).css("marginLeft","-"+jQuery.fn.nifty._niftyGP(this,"Left")+"px");
				jQuery(d).css("marginRight","-"+jQuery.fn.nifty._niftyGP(this,"Right")+"px");
				if(options.indexOf("alias") >= 0 || (color=jQuery.fn.nifty._niftyBC(this))=="transparent"){ color="transparent";bk="transparent"; border=jQuery.fn.nifty._niftyPBC(this);btype="t"; } else { bk=jQuery.fn.nifty._niftyPBC(this); border=jQuery.fn.nifty._niftyMix(color,bk); }
				jQuery(d).css("background",bk);
				d.className="niftycorners";
				p=jQuery.fn.nifty._niftyGP(this,"Bottom");
				if(options.indexOf("small") >= 0){
					jQuery(d).css("marginTop",(p-2)+"px");
					btype+="s"; lim=2;
					}
				else if(options.indexOf("big") >= 0){
					jQuery(d).css("marginTop",(p-10)+"px");
					btype+="b"; lim=8;
					}
				else jQuery(d).css("marginTop",(p-5)+"px");
				for(i=lim;i>0;i--)
					jQuery(d).append(jQuery.fn.nifty.CreateStrip(i,bottom,color,border,btype));
				jQuery(this).css("paddingBottom", "0");
				jQuery(this).append(d);			
			};
		});

		/* Make each block the height of the largest, if required. */
		if(options.indexOf("height") >= 0){
			var maxHeight=0;
			var minHeight = ($.browser.msie) ? 'height' : 'min-height';
			var gap = 0;
			this.each(function(){
				if (this.offsetHeight>maxHeight) {maxHeight=this.offsetHeight;}
			});
			this.each(function(){
				gap = ((maxHeight-this.offsetHeight));
				if(gap > 0 | $.browser.msie) {
					// TODO: create elements only through jQuery, e.g. jQuery("<b></b>");
					var t=document.createElement("b");
					jQuery(t).addClass("niftyfill").css(minHeight, gap + "px");
					nc=this.lastChild;
					nc.className=="niftycorners" ? this.insertBefore(t,nc) : jQuery(this).append(t);
				}
			});
		}
	}

	jQuery.fn.nifty.CreateStrip = function(index,side,color,border,btype){
		// TODO: create elements only through jQuery
		var b=document.createElement("b");
		b.className=btype+index;
		jQuery(b).css("backgroundColor", color).css("borderColor", border);
		if(side=="left") jQuery(b).css("borderRightWidth", "0").css("marginRight", "0");
		else if(side=="right") jQuery(b).css("borderLeftWidth", "0").css("marginLeft", "0");
		return(b);
	}

	jQuery.fn.nifty._niftyPBC = function(x){
		var el=x.parentNode,c;
		while(el.tagName.toUpperCase()!="HTML" && (c=jQuery.fn.nifty._niftyBC(el))=="transparent")
			el=el.parentNode;
		if(c=="transparent") c="#FFFFFF";
		return(c);
	}

	jQuery.fn.nifty._niftyBC = function(x){
		var c=jQuery(x).css("backgroundColor");
		if(c==null || c=="transparent" || c.indexOf("rgba(0, 0, 0, 0)") >= 0) return("transparent");
		if(c.indexOf("rgb") >= 0) {
			var hex="";
			var regexp=/([0-9]+)[, ]+([0-9]+)[, ]+([0-9]+)/;
			var h=regexp.exec(c);
			for(var i=1;i<4;i++){
				var v=parseInt(h[i]).toString(16);
				if(v.length==1) hex+="0"+v; else hex+=v;
			}
			c = "#"+hex;	
		}
		return(c);
	}

	jQuery.fn.nifty._niftyGP = function(x,side){
		var p=jQuery(x).css("padding"+side);
		if(p==null || p.indexOf("px") == -1) return(0);
		return(parseInt(p));
	}

	jQuery.fn.nifty._niftyMix = function(c1,c2){
		var i,step1,step2,x,y,r=new Array(3);
		c1.length==4 ? step1=1 : step1=2;
		c2.length==4 ? step2=1 : step2=2;
		for(i=0;i<3;i++){
			x=parseInt(c1.substr(1+step1*i,step1),16);
			if(step1==1) x=16*x+x;
			y=parseInt(c2.substr(1+step2*i,step2),16);
			if(step2==1) y=16*y+y;
			r[i]=Math.floor((x*50+y*50)/100);
			r[i]=r[i].toString(16);
			if(r[i].length==1) r[i]="0"+r[i];
		}
		return("#"+r[0]+r[1]+r[2]);
	}
})(jQuery);