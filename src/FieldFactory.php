<?php

namespace Brisum\Wordpress\CustomField;

use Brisum\Wordpress\CustomField\Field\Field;
use Exception;

class FieldFactory
{
	const DEFAULT_CLASS_FIELD = 'Field';

	protected function __construct() {}
	protected function __clone() {}

	/**
	 * Create field
	 *
	 * @param int $postId
	 * @param array $settings
	 * @return Field
	 * @throws Exception
	 */
	public static function createField($postId, array $settings)
	{
		if (empty($settings['type'])) {
			throw new Exception(__('Empty field type'));
		}
		if (empty($settings['view'])) {
			throw new Exception(__('Empty field view'));
		}

		$type = ucfirst(preg_replace_callback(
			'/(?:-|_)([a-z0-9])/i',
			function($matches) {
				return strtoupper($matches[1]);
			},
			$settings['type']
		));
		$view = ucfirst(preg_replace_callback(
			'/(?:-|_|\/)([a-z0-9])/i',
			function($matches) {
				return strtoupper($matches[1]);
			},
			$settings['view']
		));
		$classesChain = array(
			self::DEFAULT_CLASS_FIELD . $type . $view,
			self::DEFAULT_CLASS_FIELD . $type,
			self::DEFAULT_CLASS_FIELD
		);

		foreach ($classesChain as $className) {
			if (file_exists(BRISUM_CUSTOM_FIELD_DIR_FIELD . "{$className}.php")) {
				require_once BRISUM_CUSTOM_FIELD_DIR_FIELD . "{$className}.php";

				$className = "Brisum\\Wordpress\\CustomField\\Field\\{$className}";
				return new $className($postId, $settings);
			}
		}
		throw new Exception(__("Can't find field class"));
	}
}
