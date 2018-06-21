<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldSelectMultiple extends Field
{
	/**
	 * Normalize field value
	 *
	 * @param mixed $values
	 * @return mixed
	 */
	protected function normalize($values)
	{
		if (!is_array($values)) {
			$values = array();
		}

		foreach ($values as $key => $value) {
			$values[$key] = parent::normalize($value);
		}
		
		return $values;
	}
}
