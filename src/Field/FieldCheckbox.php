<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldCheckbox extends Field
{
	/**
	 * Normalize field value
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($value)
	{
		$value = parent::normalize($value);
		return $value ? 1 : 0;
	}
}
