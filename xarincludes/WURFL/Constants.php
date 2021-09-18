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
 * @package	WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * WURFL PHP API Constants
 * @package	WURFL
 */
class WURFL_Constants
{
    private function __construct()
    {
    }

    public const API_VERSION = "1.4.1";

    public const GENERIC = "generic";
    public const GENERIC_XHTML = "generic_xhtml";
    public const GENERIC_WEB_BROWSER = "generic_web_browser";
    public const GENERIC_MOBILE = "generic_mobile";

    public const ACCEPT_HEADER_VND_WAP_XHTML_XML = "application/vnd.wap.xhtml+xml";
    public const ACCEPT_HEADER_XHTML_XML = "application/xhtml+xml";
    public const ACCEPT_HEADER_TEXT_HTML = "application/text+html";

    public const UA = "UA";

    public const MEMCACHE = "memcache";
    public const APC = "apc";
    public const FILE = "file";
    public const NULL_CACHE = "null";
    public const EACCELERATOR = "eaccelerator";
    public const SQLITE = "sqlite";
    public const MYSQL = "mysql";

    public const NO_MATCH = null;
    public const RIS_DELIMITER = '---';
}
