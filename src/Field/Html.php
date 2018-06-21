<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldHtml extends Field
{
	/**
	 * Get default field options
	 *
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return array(
			'content' => ''
		);
	}
}
