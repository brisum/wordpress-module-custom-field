<?php

namespace Brisum\Wordpress\CustomField;

use Exception;

class View
{
	/**
	 * @param string $template
	 * @param array $vars
	 * @return void
	 * @throws Exception
	 */
	public function render($template, array $vars = array())
	{
		$templatePath = BRISUM_CUSTOM_FIELD_DIR_TEMPLATE . $template . '.tpl.php';

		if (!file_exists($templatePath)) {
			throw new Exception(__("Could not find template. Template: {$template}"));
		}
		extract($vars);
		require $templatePath;
	}

	/**
	 * @param string $template
	 * @param array $vars
	 * @return string
	 */
	public function content($template, array $vars = array())
	{
		ob_start();
		$this->render($template, $vars);
		$content = ob_get_contents();
		ob_clean();

		return $content;
	}
}
