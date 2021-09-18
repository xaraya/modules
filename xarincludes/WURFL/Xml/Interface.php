<?php
/**
 * Copyright (c) 2012 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 *
 * @category   WURFL
 * @package	WURFL_Xml
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * WURFL XML Parsing interface
 * @package	WURFL_Xml
 */
interface WURFL_Xml_Interface
{
    /**
     * Parses the given file and returns a WURFL_Xml_ParsingResult
     * object
     *
     * @param string $fileName
     * @return WURFL_Xml_ParsingResult
     */
    public function parse($fileName);

    public const ID = "id";
    public const USER_AGENT = "user_agent";
    public const FALL_BACK = "fall_back";
    public const ACTUAL_DEVICE_ROOT = "actual_device_root";
    public const SPECIFIC = "specific";

    public const DEVICE = "device";

    public const GROUP = "group";
    public const GROUP_ID = "id";

    public const CAPABILITY = "capability";
    public const CAPABILITY_NAME = "name";
    public const CAPABILITY_VALUE = "value";
}
