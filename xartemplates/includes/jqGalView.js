/*
 * jQuery jqGalView Plugin
 * Examples and documentation at: http://benjaminsterling.com/2007/08/24/jquery-jqgalview-photo-gallery/
 * This is a port of http://www.flashimagegallery.com/pics/artwork/
 *
 * @author: Benjamin Sterling
 * @version: 1.5
 * @requires jQuery v1.1.3.1 or later
 * @optional jQuery Easing v1.1.1
 * @optional jQuery jqModal v2007.08.17 +r11
 *
 * changes:
 *	08/28/2007:
 *		Added: option to have full image to open in jqModal (jqModal plugin required)
 *		Fixed: ie7 issue with margin-left and margin-top not being set
 *		Fixed: ie7 issue with "open" not being clickable
 *
 *	08/27/2007:
 *		Added: option to turn scroll off (larger image mouseover)
 *		Added: image resizing for when the scroll is turned off (borrowed from thickbox)
 *		Added: option to change the thumbnail scroll easing (easing plugin required)
 *		Added/Changed: option to ease the "open" dialog into view
 *		Added: a switch to the navigation to check if larger image is visible and if
 *				so, fade that out and the scroll the thumbnails to their respective
 *				view
 *		Added: some function globals to eleviate some duplicate code (not sure if this
 *				is good practise)
 *		Fixed: Clickablity of the "open" dialog
 *		Removed: "go back" dialog box (the thing that was following the mouse)
 *		Added: "go back" option text to title attibute of the large image
 *		Added:  a loading bar (image borrowed from thickbox)
 *		Added:  image fadeIn and fadeOut when loading and closing
 *
 * special thanks for help and direction (check url above for links to their sites):
 *		Rey Bango, Andy Matthews, Stephan Beal, Rick Faircloth, Joan Piedra, and the creators of FIG (flash iamge gallery)
 */
(function($) {
	var $loadBar,$image;
	$.fn.jqGalView = function(options){
		return this.each(function() {
			var opts = $.extend({}, $.fn.jqGalView.defaults, options);
			var $this = $(this), $els = $this.children(), title = $this.attr('title'), $img = $('img', $this);
			var header = $('<div class="gvHeader">').appendTo(opts.appendTo).append('<strong>'+title+'</strong>')
											.append('<a href="#" target="_blank" class="gvFullSizeText">'+opts.goFullSizeTxt+'</a>');
			var $holder = $('<div class="gvHolder"/>').appendTo($('<div class="gvContainer">').appendTo(opts.appendTo));
			$img.each(function(i){
				var $item = $(this);
				var $div = $('<div class="gvItem">').appendTo($holder).append(this).css('cursor','pointer');
				$('<div class="gvOpen">'+opts.openTxt+'</div>').appendTo($div).css({top:-16,opacity:.75});
				
				
				$div
				.hover(
					function(){
						$(this).children('.gvOpen').animate({top:0},'fast', opts.ease);
					},
					function(){
						$(this).children('.gvOpen').animate({top:-16},'fast', opts.ease);
					}
				);
				$item
				.click(
					function(){
						$.fn.jqGalView.view(this,$mainImgContainer,opts);
					}
				);
				$item.siblings('.gvOpen')
				.click(
					function(){
						$item.trigger('click');
					}
				);
			});
			var $mainImgContainer = $('<div class="gvImgContainer">').appendTo('.gvContainer');
			$image = $('<img/>').appendTo($mainImgContainer).css({cursor:'pointer',display:'none'});
			var $footer = $('<div class="gvLinks">').appendTo($('<div class="gvFooter">').appendTo(opts.appendTo));

			for(var i = 0; i < $img.size()/opts.items; i++){
				$('<a href="#'+(i)+'">'+(i+1)+'</a>').appendTo($footer)
					.click(function(){
						var $this = $(this);
						var index = $this.attr('href').replace(/^.*#/, '')
						var $parent = $img.eq(opts.items*index).parent().parent();
						
						if($image.is(":hidden"))
							$parent.animate({marginTop:-($mainImgContainer.height()*index)},'1000', opts.tnease);
						else
							$mainImgContainer.fadeOut(100).unbind();
							$image.fadeOut(100,function(){$parent.animate({marginTop:-($mainImgContainer.height()*index)},'1000', opts.tnease);});
						
						return false;
					});	
			};
			
			$this.remove();
		});
	};
	$.fn.jqGalView.view = function($this,$mainImgContainer,$opts){
		var $url = $this.src.replace($opts.prefix,''), $width,$height,$f_wh={},$f_whOrg ={},$w,$h, $widthOrg,$heightOrg;
		if(typeof $loadBar == 'undefined')	$loadBar = $('<div class="gvLoader"/>').appendTo($('.gvContainer'));
		$loadBar.show();

		var w = $mainImgContainer.width();
		var h = $mainImgContainer.height();
		$mainImgContainer.show();
		
		$image.attr({src:$url,title:$opts.backTxt}).css({top:0,left:0,position:'absolute'}).hide();
		$('.gvFullSizeText').attr('href',$url).show();
		
		$img = new Image();
		$img.onload = function(){
			$img.onload = null;
			$width = $widthOrg = $img.width;
			$height = $heightOrg = $img.height;

			if($opts.scroll){
				$w = w-$width ;
				$h = h-$height ;
				$mainImgContainer
				.css({top:0,left:0,position:'absolute', cursor:'pointer'})
				.mouseout(function(e){
					$image.animate($f_whOrg,'fast', $opts.ease);
				})
				.mousemove(function(e){
					var curX, curY;
					if($.browser.msie){
						curX = e['x'];
						curY = e['y'];
					}
					else{
						curX = e['layerX'];
						curY = e['layerY'];						
					}
					if(h>$height) $f_wh = {marginLeft:((w-$width)*(curX/w))};
					else if(w>$width) $f_wh = {marginTop:((h-$height)*(curY/h))};
					else $f_wh = {marginTop:((h-$height)*(curY/h)),marginLeft:((w-$width)*(curX/w))};
					$image.css($f_wh);
				});
			}
			else{
				if ($width > w) {
					$height = $height * (w / $width); 
					$width = w; 
					if ($height > h) { 
						$width = $width * (h / $height); 
						$height = h; 
					}
				} else if ($height > h) { 
					$width = $width * (h / $height); 
					$height = h; 
					if ($width > w) { 
						$height = $height * (w / $width); 
						$width = w;
					}
				}
				$image.css({width:$width,height:$height, marginLeft:(w-$width)*.5,marginTop:(h-$height)*.5})
			};
			
			if($opts.modal){
				$('.gvFullSizeText').click(function(){
					$.fn.jqGalView.buildDialogBox(this.href,$widthOrg,$heightOrg);
					return false;
				});
			};
			
			$mainImgContainer
			.click(function(e){
				$('.gvFullSizeText').hide();
				$mainImgContainer.fadeOut();
				$mainImgContainer.unbind();
				$image.fadeOut();
			});

			$f_whOrg = {width:$width,height:$height, marginLeft:(w-$width)*.5,marginTop:(h-$height)*.5};
			$image.css($f_whOrg);

			$loadBar.fadeOut('fast',function(){$image.fadeIn();});
			
		};
		$img.src = $url;
		
	};
$.fn.jqGalView.buildDialogBox = function($url, $width, $height){
	
	$('#gvModal').remove();
	$('body').append('<div id="gvModal" class="jqmWindow">');
	$gvModal = $('#gvModal');
	var w = $gvModal.width();
	var h = $gvModal.width();
	
	if ($width > w) {
		$height = $height * (w / $width); 
		$width = w; 
		if ($height > h) { 
			$width = $width * (h / $height); 
			$height = h; 
		}
	} else if ($height > h) { 
		$width = $width * (h / $height); 
		$height = h; 
		if ($width > w) { 
			$height = $height * (w / $width); 
			$width = w;
		}
	}

	var $img = $('<img src="'+$url+'"/>').appendTo($gvModal).css({width:$width,height:$height,display:'none',padding:0});
	$('#gvModal').jqm({zIndex:5000,modal:false,overlay:50,
		onHide: function(hash, serial){
			hash.o.remove();
			hash.w.remove();
		},
		onShow: function(hash){
			hash.w.fadeIn('slow',function(){$img.fadeIn();});
		}
	}).jqmShow(); 
};
	$.fn.jqGalView.defaults = {
		prefix: 'thumbnail.',
		items: 20,
		appendTo: 'body',
		openTxt:'open&raquo; ',
		backTxt:'<< Click to go back',
		goFullSizeTxt: 'Full Size',
		ease: null,
		tnease:null,
		scroll: false,
		modal : false
	};
})(jQuery);