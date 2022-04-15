
## Overview

### What is it?

*Images is a Xaraya Utility Module.* It is currently responsible for
basic image manipulation, including the following:

  - Resize Image files
  - Crop image files
  - Rotate image files
  - Add text to an image file (think buttons here...)
  - ...

### How to use it?

#### Image Tags

\<xar:image-resize src="FILEID" \[width="(\[0-9\]+)(px|%)"
\[height="(\[0-9\]+)(px|%)"\]\] \[constrain="0|1"\] label="TEXT" /\>

The IMAGE tag is useful for displaying (cached) images at different
proportions. Images are resized according to the height/width values you
specify with the resulting image cached for faster displaying later on.
Note: Any other attribute not listed will be passed directly on to the
resultant \<img\> tag.

Examples:

Resize an image's width while keeping the proportions:  
  
\<xar:image-resize src="23" width="92px" constrain="1" label="my logo"
/\>

Apply some pre-defined image processing (e.g. watermark):  
  
\<xar:image-resize src="test/image.jpg" setting="JPEG 800 x 600"
label="my image" /\>

### Included Blocks

There are no included blocks.

### Privileges

The Images module only has the Admin privilege to assign. You will need
this level if you want to set the general settings for the Images
module. The Images module interacts with the uploads module, so make
sure you set appropriate levels for that module as well. Take special
note on the approval of files in the uploads module. Only files that
have been approved will normally be accessible by standard users, or you
need to assign them the EDIT privilege for the uploads.

### Upgrade from version 1.0.0

The naming convention for derivative images has changed since version
1.0.0. So you can delete the old derivative images, and new derivative
images will be created as needed in the same directory...

### Further Information

Extended information about this module can be found at [Xaraya Extension
and
Releases](http://www.xaraya.com/index.php/release/152.html "Images Module - Xaraya Extension 152").

  - Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on Images found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/images/ "Related documentation on Images")

** Images module - Overview**  
 Version 1.1.0  

