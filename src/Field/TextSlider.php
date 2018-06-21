<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldTextSlider extends Field
{
	/**
	 * Get default field options
	 *
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return array(
			'textBefore' => '',
			'textAfter' => '',
			'data-start' => 0,
			'data-end' => 100,
			'step' => 1
		);
	}

	/**
	 * Normalize field value
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($value)
	{
		$min = isset($this->options['data-start']) ? $this->options['data-start'] : 0;
		$max = isset($this->options['data-end']) ? $this->options['data-end'] : 100;
		$step = isset($this->options['step']) ? absint($this->options['step']) : 1;
		$value = parent::normalize($value);

		if ($value < $min) {
			$value = $min;
		}
		if ($value > $max) {
			$value = $max;
		}
		if ($remainder = $value % $step) {
			$value = max($min, $value - $remainder);
		}

		return $value;
	}
}
