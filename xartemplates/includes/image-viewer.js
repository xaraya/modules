// Created by Andreas Lagerkvist http://exscale.se
// Find links that point to images and run ImageViewerDisplay() onclick
$.fn.ImageViewer = function()
{
	return this.each(function()
	{
		var t = $(this);

		// Get all links pointing to images in the scope
		t.find('a[@href$="jpg"], a[@href$="bmp"], a[@href$="gif"], a[@href$="png"]').each(function(i)
		{
			// Show ImageViewer onclick
			$(this).click(function()
			{
				t.ImageViewerDisplay(i+1);
				return false;
			});
		});
	});
}

// Create img viewer
$.fn.ImageViewerDisplay = function(currentImgNum)
{
	return this.each(function()
	{
		// Get all links that point to images in this scope
		currentImgNum	= parseInt(currentImgNum);
		var t			= $(this);
		var imgs		= t.find('a[@href$="jpg"], a[@href$="bmp"], a[@href$="gif"], a[@href$="png"]');
		var numImgs		= imgs.length;

		if(numImgs < 1)	return false;

		// Add background if not added
		if(!document.getElementById('image-viewer-background'))
		{
			$('<div id="image-viewer-background"></div>')
			.appendTo('body')
			.css({opacity: '0', background: '#000', position: 'absolute', left: '0', top: '0', width: '' +$(document).width() +'px', height: '' +$(document).height() +'px'})
			.hide()
			.fadeTo(300, 0.8, function()
			{
				ImageViewerDoTheRest();
			});
		}
		else
		{
			ImageViewerDoTheRest();
		}

		function ImageViewerDoTheRest()
		{
			// Make sure current image isn't out of bounds
			if(currentImgNum < 1)				currentImgNum = 1;
			else if(currentImgNum > numImgs)	currentImgNum = numImgs;

			// Get current img
			var currentImg	= imgs.eq(currentImgNum-1);

			// Preload image so that centering works properly
			var preload = new Image();
			preload.src = currentImg.attr('href');

			// Add loading div if img is not already in cache
			if(preload.complete)
			{
				ImageViewerDoTheEnd();
			}
			else
			{
				$('<div id="image-viewer-loading">Loading...</div>').appendTo('body').fadeIn(300).center();

				preload.onload = function()
				{
					ImageViewerDoTheEnd();
				}
			}

			function ImageViewerDoTheEnd()
			{
				// Remove loading div
				$('#image-viewer-loading').fadeOut(300, function()
				{
					$(this).remove();
				});

				// Get all information about the image
				var title		= (currentImg.attr('title')) ? '<h2>' +currentImg.attr('title') +'</h2>' : '';
				var imgsrc		= '<p id="image-viewer-image"><img src="' +currentImg.attr('href') +'" alt="" /></p>';
				var caption		= (currentImg.find('img').attr('alt')) ? '<p id="image-viewer-caption">' +currentImg.find('img').attr('alt') +'</p>' : '';

				// Create navigation (if there's more than one image)
				if(numImgs > 1)
				{
					var navigation	= '<ul id="image-viewer-nav">';
					navigation	   += (currentImgNum != 1) ? '<li id="image-viewer-previous"><a href="#" title="Go to Previous Image">Previous</a></li>' : '<li id="image-viewer-previous">Previous</li>';
					navigation	   += (currentImgNum != numImgs) ? '<li id="image-viewer-next"><a href="#" title="Go to Next Image">Next</a></li>' : '<li id="image-viewer-next">Next</li>';
					navigation	   += '</ul>';

					var imgpos		= '<p id="image-viewer-image-pos">Image ' +currentImgNum +' of ' +numImgs +'</p>';
				}
				else
				{
					var navigation = '';
					var imgpos = '';
				}

				// Create image-viewer
				var imageViewer	= '<div id="image-viewer">' +title +imgsrc +caption +imgpos +navigation +'</div>';

				// Animate differently depending on if old image-viewer exists or not
				if(document.getElementById('image-viewer'))
				{
					$('#image-viewer').remove();
					$(imageViewer).appendTo('body').center();
					addImageViewerNavigationOnClicks();
				}
				else
				{
					$(imageViewer).appendTo('body').center().hide().slideDown(500, addImageViewerNavigationOnClicks);
				}

				// Adds onclicks to navigation
				function addImageViewerNavigationOnClicks()
				{
					// Previous button
					$('#image-viewer-previous a').click(function()
					{
						t.ImageViewerDisplay(currentImgNum - 1);
						return false;
					});
					// Next button
					$('#image-viewer-next a').click(function()
					{
						t.ImageViewerDisplay(currentImgNum + 1);
						return false;
					});
					// Actual image
					$('#image-viewer-image a').click(function()
					{
						location.href = $(this).attr('href');
					});
					// Close on click
					$('#image-viewer').click(function()
					{
						$('#image-viewer').slideUp(300, function()
						{
							$(this).remove();
							$('#image-viewer-loading').fadeOut(300, function()
							{
								$(this).remove();
							});
							$('#image-viewer-background').fadeOut(300, function()
							{
								$(this).remove();
							});
						});
						return false;
					});
				}
			}
		}
	});
}