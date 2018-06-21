<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldCheckboxGroup extends Field
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
		
		foreach ($values as $name => $value) {
			$value = parent::normalize($value);
			$values[$name] = $value ? 1 : 0;
		}

		return $values;
	}
}
