
## Overview

### What is it?

*Uploads is a Xaraya Utility Module.* It is currently responsible for
accomplishing these tasks:

  - Allows for the importing of files into the Xaraya system either
    locally or externally.
  - Allows the managing of access to files via privileges.
  - ...

## How to use it?

### External Imports and Uploads

Files that are uploaded or imported from external locations can be saved
in the database (DB) or stored in the filesystem (FS). Use the options
(FS/DB) under each form to select the storage location for your file.

The import engine will try to make a best guess for the name of the file
you are trying to import. In the event that it cannot figure out what
the file name is it will use the following methods:

  - **HTTP and HTTPS**
    
    In the event that the filename for an HTTP import can not be
    determined, the file will be named based on the host name of the
    server the file was imported from. For example, importing
    http://www.xaraya.com/ would return a file with the name of
    www\_xaraya\_com.html

  - **FTP**
    
    Due to the underlying nature of the FTP protocol, it is impossible
    to successfully access a file on a system without providing a viable
    file name. In this case, no file name 'guessing' will be attempted
    and the import will fail.

### Local Imports Sandbox

Files can be inserted into your local import sandbox and used
immediately by users with sufficient access to add files and attach them
to hooked objects (articles, comments, etc). Files you place in your
local import sandbox can be seen in the 'Import Local Files' form
immediately upon adding them.

### Privileges

Uploads has the following privilege levels:

  - ViewUploads with the READ level of access to a file
  - AddUploads with the ADD level of access to add files to the uploads
    system
  - EditUploads with the EDIT level to edit files that have been added
    to the uploads system
  - ManageUploads with the Delete level to remove files from the uploads
    system.
  - AdminUploads for the admin access. Only site administrators need
    this level

A role needs the ViewUploads privilege to view a file that has been
uploaded and approved by the admin/editor. If the file has not been
approved, but submitted, the Edit privilege is needed to access the
file. You can use the autoapprove option to automatically approve all
files uploaded by a certain user.

### Included Blocks

There are no included blocks.

  

### Included Hooks

The Uploads module does provide a transform hook. You can activate the
uploads hook for a module or itemtype by going to [Modules - Configure
Hooks - Uploads Module](&xar-modurl-modules-admin-hooks;&hook=uploads).
From there check the radio buttons next to the modules and itemtypes
where you wish to activate the uploads module hooks.  
If you combine the hook with the upload property, then you can add
parameters to the dynamic property. Set the options with:
*single;methods(-trusted,+external,+upload,+stored);basedir(/tothedir/uploads/{user});importdir(/tothedir/uploads/{user})*  
This option will allow for a single upload, with external, uploaded and
stored files, but not trusted ones. The basedir to look for stored files
is the user dir, and new files will be placed there as well.

The Uploads module does also provide a display hook with waiting content
waiting content hook by going to [Modules - Configure Hooks - Uploads
Module](&xar-modurl-modules-admin-hooks;&hook=uploads). From there check
the radio buttons next to the base module.  
Then make sure you have the waiting content block available on your
site. When a file is in submitted status, it will be shown in the block.

### DD Uploads Methods

  - Trusted  
    A "Trusted" directory usually points to a folder that lists files
    previously uploaded, eg, by the Administrator via FTP. Please note,
    if configured, multiple items can be selected in this input method.
  - External  
    As the name implies, this input expects a valid URL, either HTTP or
    FTP. Please note, PHP/HTTP must be configured to allow outward-bound
    connections.
  - Upload  
    The standard "upload" input, allowing the user to upload a file from
    their computer. Currently, files can only be uploaded one at a time.
  - Stored  
    This input represents the library of files uploaded. If selected,
    the user will be able to select from previously uploaded files.
    Please note, if configured, multiple items can be selected in this
    input method.

### Configuring the DD Uploads Property

Most of the global Modify Config options can be overridden on a
per-property case, allowing greater flexibility to admins to store files
separately based on content or user. The following is a brief overview
of the options available:

  - Multiple?  
    Checking this option will allow multiple files to be selected in the
    Trusted or Stored methods.
  - Input Method  
    You can override the global config with your own Input Methods
    preference for this particular instance of the Uploads property.
  - File Types  
    Allowed file types.
  - Base Directory  
    Override the global config with a property specific path.
  - Trusted Directory  
    Override the global config with a property specific path.
  - Directory Name  
    This is the directory prefix for the module to use when creating sub
    directories. Uploads recognizes several macros, {user} and {theme}
    to dynamically create certain path conventions.

### Further Information

Further information on the Uploads module can be found at

  - Uploads Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/36.html "Uploads Module - Xaraya Extension 666").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on Uploads found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/uploads/ "Related documentation on Uploads")

** Uploads Module Overview**  
 Version 1.0.0  2006-03-08

