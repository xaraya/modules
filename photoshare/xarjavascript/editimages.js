// =======================================================================
// Photoshare by Jorn Lind-Nielsen (C) 2002.
// ----------------------------------------------------------------------
// For POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// =======================================================================

var currentImage   = null;  // Points to the selected image when only one image is selected
var selectedImages = {};    // Contains set of selected images
var imageCount     = 0;     // Number of selected images


// =======================================================================
// Event handlers
// =======================================================================

function toggleSelectImage(img)
{
  if (img.className == "photoshare-selected")
    unselectImage(img);
  else
    selectImage(img);
}


function unselectImage(img)
{
  img.className = "photoshare-unselected";
  --imageCount;
  selectedImages[img.id] = false;

  if (imageCount == 1)
    currentImage = img;
  else
    currentImage = null;
}


function selectImage(img)
{
  img.className = "photoshare-selected";
  ++imageCount;
  selectedImages[img.id] = true;

  if (imageCount == 1)
    currentImage = img;
  else
    currentImage = null;
}


function toggleSelectImageRange(img)
{
  var images = document.images;
  var inRange = false;
  var doSelect = (img.className == 'photoshare-selected' ? false : true);

  for (var i=images.length-1; i>=0; --i)
  {
    if (images[i].id == img.id)
      inRange = true;

    if (doSelect)
    {
      if (images[i].className == 'photoshare-selected')
        inRange = false;
      if (images[i].className == 'photoshare-unselected'  &&  inRange)
        selectImage(images[i]);
    }
    else
    {
      if (images[i].className == 'photoshare-unselected')
        inRange = false;
      if (images[i].className == 'photoshare-selected'  &&  inRange)
        unselectImage(images[i]);
    }
  }
}


function unselectAllImages()
{
  var images = document.images;

  for (var i=images.length-1; i>=0; --i)
  {
    if (images[i].className == 'photoshare-selected')
      unselectImage(images[i]);
  }
}


function handleOnMouseDownImage(imgElement, evt)
{
  evt = (evt ? evt : (event ? event : null));
  if (evt == null)
    return true;

  if (evt.ctrlKey)
  {
    toggleSelectImage(imgElement);
    return false;
  }
  else
  if (evt.shiftKey)
  {
    toggleSelectImageRange(imgElement);
    return false;
  }
  else
  {
    var selected = (imgElement.className == "photoshare-selected");
    unselectAllImages();
    if (selected)
      unselectImage(imgElement);
    else
      selectImage(imgElement);
    return false;
  }

  return true;
}


function handleOnClickTarget(target)
{
  if (imageCount == 0)
  {
    alert(translations.selectImage);
  }
  else
  if (imageCount == 1)
  {
    if (currentImage == null)
      alert("Internal error: unexpected missing current image");

    var imageID  = currentImage.id;
    var position = target.id;
	var authkey = document.getElementById('authid').value;

    window.location = "index.php?module=photoshare&func=moveimage&iid=" + imageID + "&pos=" + position + "&authid=" + authkey;
  }
  else
  {
    alert(translations.tooManyImages);
  }
}


function handleOnClickCommand(command, requireSelectedImages)
{
    // Check if any image is selected
  if (imageCount == 0  &&  requireSelectedImages)
  {
    alert(translations.selectImage);
    return;
  }

    // Confirm delete
  if (command == "delete")
    if (!confirm(translations.confirmDelete))
      return;

    // Build image id list

    // Iterate through image set and create comma-sep. list in string
  var idString = "";
  for (var i in selectedImages)
  {
    if (selectedImages[i])
    {
      if (idString == "")
        idString += i;
      else
        idString += "," + i;
    }
  }

    // Put image id list and command into form and submit it

  var commandForm = document.forms["commandForm"];
  commandForm.command.value = command;
  commandForm.imageids.value = idString;

  commandForm.submit();
}


/*=============================================================================
  Context dependent menus
=============================================================================*/

var contextmenu =
{
  currentImgElement:  null,
  actionURLs:         null
};


contextmenu.onMouseDown = function(imgId, evt, menuId)
{
  evt = (evt ? evt : (event ? event : null));
  if (evt == null)
    return true;

  var imgElement = document.getElementById(imgId);

  var menuDivElement = document.getElementById(menuId);

  var pos = getPositionOfEvent(evt);

  psmenu.openMenu(contextmenu, menuDivElement, pos);

  contextmenu.currentImgElement = imgElement;

  return true;
}


//-[ Listener methods for psmenu ]---------------------------------------------

contextmenu.itemSelected = function(menuId, itemIndex)
{
	if (contextmenu.confirmations && contextmenu.confirmations[itemIndex])
		if (!confirm(contextmenu.confirmations[itemIndex]))
			return;

	var url = contextmenu.actionURLs[itemIndex] + "&iid=" + contextmenu.currentImgElement.id;
	window.location.href = url.replace(/&amp;/ig, '&');
}


contextmenu.menuClosed = function()
{
  contextmenu.currentImgElement = null;
}

