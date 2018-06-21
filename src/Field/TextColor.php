<?php

namespace Brisum\Wordpress\CustomField\Field;

class FieldTextColor extends Field
{
	const FORMAT_RGB = 'rgb';
	const FORMAT_RGBA = 'rgba';
	const FORMAT_HEX = 'hex';

	/**
	 * Normalize field value
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($value)
	{
		$format = isset($this->options['color-format']) ? $this->options['color-format'] : null;
		$value = parent::normalize($value);

		if (self::FORMAT_RGB === $format && isset($this->options['colorpicker']['order']['opacity'])) {
			$format = self::FORMAT_RGBA;
		}

		switch ($format) {
			case self::FORMAT_RGB:
				return preg_match('/rgb\([0-9]{1,3}, [0-9]{1,3}, [0-9]{1,3}\)/', $value)
					? $value
					: null;
			case self::FORMAT_RGBA:
				return preg_match('/rgba\([0-9]{1,3}, [0-9]{1,3}, [0-9]{1,3}, (0|1|0\.[0-9]{1,2})\)/', $value)
					? $value
					: null;
			case self::FORMAT_HEX:
				return preg_match('/#[0-9a-f]{6}/i', $value)
					? $value
					: null;
			default:
				return null;
		}
	}
}
