
## Overview

### What is it?

The HTML module is a utility module for setting allowed HTML tags.

### How to use it?

Using the administration menu, you are allowed to view and set allowed
HTML tags. You can also add new tags, edit current tags and delete tags
from the system.

### Included Blocks

There are no included blocks with this module.

### Included Hooks

Two types of Transform hooks are provided. These are for generating
linebreaks. Either use the simple, or smart linebreak transform. This
will transform text with linebreaks in the text into html text with \<br
/\> tags. **Please note, if you do not want linebreaks, do not hook this
module to another. The transform hook only affects linebreaks and not
the allowed HTML of the module.**

### TODO:

The core tag registration needs to be completely re-thought to allow
this module to act as a transform rather than just a matter of setting a
configuration variable.

### Further Information

Further information on the HTML module can be found at

  - HTML Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/779.html "HTML Module - Xaraya Extension 779").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on HTML module found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/html/ "Related documentation on HTML")

** HTML Module Overview**  
 Version 1.4.0  2005-10-27

