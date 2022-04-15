
## Overview

The CKEditor module supplies the "editor" property type for use in your
objects. To use CKEditor with a Dynamic Data object, simply add one or
more "editor" properties to your object.

Configuration settings for CKEditor are found in
**[ckeditor/xartemplates/includes/ckeditor/config.js](xartemplates/includes/ckeditor/config.js)**

There, you can control the style of the editor, which buttons to include
in the toolbars, language, size, which plugins to load, etc. The
official CKEditor documentation includes a [reference for config
settings](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html) ![leave
this website](code/modules/ckeditor/xarimages/exit.png).

#### PGRFileManager

The CKEditor module comes with the
[PGRFileManager](http://pgrfilemanager.sourceforge.net/) ![leave this
website](code/modules/ckeditor/xarimages/exit.png) plugin, which is
disabled by default. To enable the file manager, uncomment this line in
CKEditor's config.js:

> //CKEDITOR.plugins.load('pgrfilemanager');

Once you've enabled pgrfilemanager, you can access it in the editor by
clicking on the link button or image button and then clicking on Browse
Server.

Options for creating and deleting directories are available in the file
manager by right-clicking on directories in the directory tree. You can
also drag directories around, and can drag directories into other
directories and drag files into directories.

The [modify config
page]() has a
number of settings for PGRFileManager:

- **rootPath** -- Root location for your directories and files. Set this
- to a directory that exists on your server.  
- **urlPath** -- Base URL used by the editor to link to your files.
- (Must be the URL equivalent of rootPath.)  
- **allowedExtensions** -- Comma-separated list of file extensions that
- are permissible to upload.  
- **fileMaxSize** -- Maximum file size (bytes) of an upload.  
- **imageMaxHeight** -- Maximum height for image uploads.  
- **imageMaxWidth** -- Maximum width for image uploads.  
- **allowEdit** -- This setting controls whether the file manager will
- allow new uploads and copying/moving/renaming/deleting of previously
- uploaded files and directories. If set to false, you will only be able
- to select from previously uploaded files.

Note: Display of file thumbnails can be buggy with Windows hosting. You
may find that files will upload but their thumbs will not always display
in the pane where you expect them.

