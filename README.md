
## Overview

### What is it?

*This module allows you to schedule Xaraya jobs at certain times of the
day/week/month (cron)*

Those jobs could be for instance :

  - sending a daily digest mail
  - expiring temporary user passwords
  - escalating a workflow process
  - doing a backup of the database
  - refreshing cache files
  - ...

### How to use it?

The scheduler module relies on other modules to actually execute
whatever jobs are scheduled, so first you need to find out which
**module functions** can be scheduled.

[Search for scheduler API
functions]()

Then you need some kind of **trigger** to wake up the scheduler - this
can be done e.g. by requesting a specific webpage automatically from
your own system via a Unix cron entry or Windows AT command, or via a
web-based scheduler service like <http://webcron.org/>, or (if you
really have no other choice) by relying on web hits from your visitors
if your site is relatively busy.

The rest will be handled by the scheduler module :-)

### Included Blocks

  - Trigger block. This block will trigger the scheduler process. You
    can hide it from view, or let administrator see the progress. The
    block will then show the time the last trigger was run.

### Included Hooks

  - to be defined

### Further Information

Further information on the Scheduler module can be found at

  - Scheduler Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/189.html "Scheduler Module - Xaraya Extension 189").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on Scheduler found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/scheduler/ "Related documentation on Scheduler")

** Scheduler Module Overview**  
 Version 1.0.1  2006-07-20

