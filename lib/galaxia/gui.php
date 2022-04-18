<?php

namespace Galaxia;

// Load configuration of the Galaxia Workflow Engine
include_once(dirname(__FILE__) . '/config.php');

include_once(GALAXIA_LIBRARY.'/gui/gui.php');

use Galaxia\Gui\GUI;

$GUI = new GUI();
