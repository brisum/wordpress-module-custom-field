<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldText extends Field
{
	protected function getDefaultOptions()
	{
		return array(
			'textBefore' => '',
			'textAfter' => '',
		);
	}
}
