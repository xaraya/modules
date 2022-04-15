
## Overview

## What is it?

The Pubsub module allows users to subscribe to events on your system
such as new articles being posted in a particular category or replies on
a forum thread. This module uses the Scheduler module to process events
and the Mail module to actually send the notifications.

## How to use it?

The pubsub module should be hooked to the categories module first, so
that your users can subscribe to events concerning a particular
category.

Then you should enable the pubsub hooks for any other module (e.g.
articles) where you want to send out create/update/delete notifications
to your users.

And finally you should specify which templates to use for each module
and how often events should be processed in "Modify Configuration".

### Included Blocks

The Pubsub module has no blocks included at this time.

### Included Hooks

The Pubsub module supports item display hooks to let your users
subscribe to events in particular categories. It also supports item
create, item update and item delete hooks to inform your users when a
module item is created, updated or deleted. This can be configured in
the modules module.

Further information on the Pubsub module can be found at

  - Pubsub Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/181.html "Pubsub Module - Xaraya Extension 181").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on Pubsub found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/pubsub/ "Related documentation on Pubsub")

** Pubsub Module Overview**  
 Version 1.0.0  2006-04-10
