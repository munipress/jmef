<?php

/**
 * @defgroup plugins_generic_jmef Journal Metadata Exchange Format Plugin
 */
 
/**
 * @file plugins/generic/jmef/index.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_jmef
 * @brief Wrapper for jmef plugin.
 *
 */
require_once('JmefPlugin.inc.php');

return new JmefPlugin();


