//=============================================================================
// Stand alone image selector for Photoshare
// (C) Jorn Lind-Nielsen
//=============================================================================

//=============================================================================
// External interface functions
//=============================================================================

  // onClick handler for "find image" button in external program
function photoshareFindImage(inputID, photoshareURL)
{
  window.open(photoshareURL+"&targetID="+inputID, "", "width=400,height=180,resizable");
}


//=============================================================================
// Internal stuff
//=============================================================================

  // OnLoad handler ensures an initial image is selected
function handleOnLoad()
{
  selectFolder(initialFolderID);

    // Move focus initially to folder selector
  var folderSelect = document.getElementById("folderSelect");
  folderSelect.focus();
}


  // A new folder is selected (user changed value of folder selector)
function handleFolderSelect(folderSelect)
{
  var folderID = folderSelect.value;
  selectFolder(folderID);
}


  // Do actual folder selction
function selectFolder(folderID)
{
    // Get set of images in the selected folder
  var imageSet = imageInfo[folderID];

    // Resize image selector for new number of options (images)
  var imageSelect = document.getElementById("imageSelector");
  imageSelect.options.length = imageSet.length;
 
    // Iterate through alle images and create new selector options for each of them
  for (var i in imageSet)
  {
    imageSelect.options[i].value = imageSet[i].id;
    imageSelect.options[i].text = imageSet[i].title;
  }

    // Select the first image
  selectImage(imageSet[0].id);
}


  // User changed value of image selector
function handleImageSelect(imgSelect)
{
  var imageID = imgSelect.value;
  selectImage(imageID);
}


  // Do actual updates related to changing of image
function selectImage(imageID)
{
    // Find thumbnails image DOM element
  imageURL = thumbnailBaseURL + imageID;

    // Update image with new source address
  var imageElement = document.getElementById("previewImage");
  imageElement.src = imageURL;
}


  // User clicks on "select image" button
function handleOnClickSelect(URLMode, HTMLMode)
{
    // Where to insert the calculate URL
  var targetInputElement = window.opener.document.getElementById(targetInputID);

    // Get URL of selected image
  var imageElement = document.getElementById("previewImage");
  var url = imageElement.src;
  var html = url;

    // Remove "thumbnail" setting from URL
  url = url.replace(/&thumbnail=1/, "");

    // Strip absolute part of URL if requested
  if (URLMode != "absolute")
  {
    var startPos = url.indexOf("index.php?");
    url = url.substr(startPos);
  }

    // Add <IMG> tag around image if requested
  if (HTMLMode == 'img')
  {
    html = "<img src=\"" + url + "\"/>";
  }
  else
    html = url;

    // Paste image data into original input/textarea element

  if (targetInputElement.tagName == 'INPUT')
  {
      // Simply overwrite value of input elements
    targetInputElement.value = html;
  }
  else if (targetInputElement.tagName == 'TEXTAREA')
  {
      // Try to paste into textarea - technique depends on browser

    if (typeof document.selection != "undefined")
    {
        // IE: Move focus to textarea (which fortunately keeps its current selection) and overwrite selection
      targetInputElement.focus();
      window.opener.document.selection.createRange().text = html;
    }
    else if (typeof targetInputElement.selectionStart != "undefined")
    {
        // Mozilla: Get start and end points of selection and create new value based on old value
      var startPos = targetInputElement.selectionStart;
      var endPos = targetInputElement.selectionEnd;
      targetInputElement.value = targetInputElement.value.substring(0, startPos)
                                 + html
                                 + targetInputElement.value.substring(endPos, targetInputElement.value.length);
    } 
    else 
    {
        // Others: just append to the current value
      targetInputElement.value += html;
    }
  }

  window.close();
}


function handleOnClickCancel()
{
  window.close();
}

