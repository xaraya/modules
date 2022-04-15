
### Overview

The WURFL module is a simple wrapper module around the WURFL API and
database.

WURFL (Wireless Universal Resource FiLe) is a compendium of all (?) user
agents available on the Internet. More general information on WURFL can
be found [here](http://en.wikipedia.org/wiki/WURFL). For developer
information on the WURFL project's PHP API please go
[here](http://wurfl.sourceforge.net/php_index.php).

This module lets you use WURFL from within Xaraya. It has two API
functions, wurfl\_userapi\_get\_device and
wurfl\_userapi\_check\_device. The former returns an object modeling the
requesting device (browser, iPad etc.), while the latter checks that
device against a device ID entered (for instance generic\_web\_browser).

The module has no user interface, and a single admin page for testing
WURFL [here]().

WURFL can be run in two modes: accuracy and perfomrance. The latter is
the default, allowing for faster checks. Its output for any PC browser
for instance will be a generic device descrioption. The accuracy mode
can be used when speed is not as crucial, and an exact identification of
the calling device (e.g. firefox\_11\_0) is required.

The workings of the API functions are simple and should be clear from
examining the test page.

Note that after installing the module, the first time the test page (or
any query of the WURFL database) is run, there will be a lag that can
last up to 5 min while the module unpacks its database and sets up the
required files for caching and persistence.

