
## Overview

### What is it?

*The Hitcount module is a simple utility module to count the number of
hits on an item.* It is currently responsible for accomplishing these
tasks:

  - Collects and displays hit detail on items in Xaraya.

### How to use it?

Simply set the configuration as you would like, and the hitcount module
will begin collecting data on the hooked items or properties that you
configure.

### Included Blocks

There are no included blocks

### Included Hooks

The hitcount module must be hooked into the items that you want to count
up.

### Included Properties

Or you can add the Hit Count property directly to your dynamic data
objects. In this case the showOutput method wil show the number of hits.
and the showInput method will show a textbox that lets you change the
number of hits (cheating). In all cases the coordinates of the page are
stored as module/itemtype/itemid. For dataobjects this will mean module
= 182 and itemtype = objectid - 1.

