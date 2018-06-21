<?php

use Brisum\Wordpress\CustomField\CustomField;

define('BRISUM_CUSTOM_FIELD_DIR', dirname(__FILE__) . '/');
define('BRISUM_CUSTOM_FIELD_DIR_FIELD', BRISUM_CUSTOM_FIELD_DIR . 'Field/');
define('BRISUM_CUSTOM_FIELD_DIR_TEMPLATE', BRISUM_CUSTOM_FIELD_DIR . 'template/');
define('BRISUM_CUSTOM_FIELD_URL', '/vendor/brisum/wordpress/CustomField');
define('BRISUM_CUSTOM_FIELD_BODY_CLASS', 'brisum-custom-field');

CustomField::getInstance()->init();
