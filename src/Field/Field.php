<?php

namespace Brisum\Wordpress\CustomField\Field;

use Brisum\Wordpress\CustomField\FieldFactory;
use Brisum\Wordpress\CustomField\View;

class Field
{
	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $postId;

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Field view
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * Flag whether field is locked
	 *
	 * @var bool
	 */
	protected $isLock;

	/**
	 * Prefix field name
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Field attribute id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Field name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Default value
	 *
	 * @var mixed
	 */
	protected $default;

	/**
	 * Html field tag attributes
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * Field label
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * Field description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Content before field
	 *
	 * @var string
	 */
	protected $contentBefore;

	/**
	 * Content field
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Content after field
	 *
	 * @var string
	 */
	protected $contentAfter;

	/**
	 * Callable function for sanitize field value
	 *
	 * @var string|array
	 */
	protected $cbSanitize;

	/**
	 * Field options
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Field dependents
	 *
	 * @var array
	 */
	protected $dependents;

	/**
	 * List subfields
	 *
	 * @var null|Field[]
	 */
	protected $fields;

	/**
	 * Flag whether field has parent
	 *
	 * @var bool
	 */
	protected $isSubField = false;

	/**
	 * @constructor
	 * @param int $postId
	 * @param array $settings
	 */
	final public function __construct($postId, array $settings)
	{
		$this->postId = $postId;
		$this->setSettings($settings);
		$this->initSubFields();
	}

	/**
	 * Set field settings
	 *
	 * @param array $settings
	 * @return void
	 */
    private function setSettings(array $settings)
	{
		// fill settings by default values
		$settings = array_merge(
			array(
				'type' => '',
				'view' => 'default',
				'is_lock' => false,
				'prefix' => null,
				'id' => null,
				'name' => '',
				'default' => null,
				'attributes' => array(),
				'label' => '',
				'description' => '',
				'contentBefore' => '',
				'content' => '',
				'contentAfter'  => '',
				'cb_sanitize'  => '',
				'options' => array(),
				'fields' => array(),
			),
			$settings
		);

		$this->type = is_string($settings['type']) ? $settings['type'] : null;
		$this->view = is_string($settings['view']) ? $settings['view'] : null;
		$this->isLock = (bool) $settings['is_lock'];
		$this->prefix = $settings['prefix'] ? $settings['prefix'] : null;
		$this->id = $settings['id'] ? esc_attr($settings['id']) : self::randId();
		$this->name = is_string($settings['name']) ? $settings['name'] : null;
		$this->default = $settings['default'];
		$this->attributes = is_array($settings['attributes']) ? $settings['attributes'] : array();
		$this->label = is_string($settings['label']) ? $settings['label'] : null;
		$this->description = is_string($settings['description']) ? $settings['description'] : null;
		$this->contentBefore = is_string($settings['contentBefore']) ? $settings['contentBefore'] : null;
		$this->content = is_string($settings['content']) ? $settings['content'] : null;
		$this->contentAfter = is_string($settings['contentAfter']) ? $settings['contentAfter'] : null;
		$this->cbSanitize = is_string($settings['cb_sanitize']) ? $settings['cb_sanitize'] : null;
		$this->options = array_merge(
			$this->getDefaultOptions(),
			is_array($settings['options']) ? $settings['options'] : array()
		);
		$this->dependents = isset($settings['dependents']) && is_array($settings['dependents'])
			? $settings['dependents']
			: array();
		$this->fields = is_array($settings['fields']) && !empty($settings['fields']) ? $settings['fields'] : null;
	}

	/**
	 * Init subfields, if field is composite
	 *
	 * @return void
	 */
	private function initSubFields()
	{
		if ($this->fields) {
			foreach ($this->fields as $key => $subFieldSettings) {
				$subFieldSettings['is_lock'] = $this->isLock;
				$subFieldSettings['prefix'] = $this->prefix . $this->name;

				$field = FieldFactory::createField($this->postId, $subFieldSettings);
				$field->isSubField = true;
				$this->fields[$key] = $field;
			}
		}
	}

	/**
	 * Get setting value by name
	 *
	 * @param string $name
	 * @return mixed
	 */
	final public function get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}

	/**
	 * Render field
	 *
	 * @param mixed $value
	 * @return string
	 */
	final public function content($value)
	{
		return $this->fields ? $this->contentSubFields($value) : $this->contentField($value);
	}

	/**
	 * Render field content
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function contentField($value)
	{
		$view = new View();
		return $view->content("field/{$this->type}/{$this->view}", $this->getData($value));
	}

	/**
	 * Render subfields content
	 *
	 * @param mixed $values
	 * @return string
	 */
	private function contentSubFields($values)
	{
		$view = new View();
		$data = $this->getData($values);

		if ($this->fields) {
			$data['fields'] = array();
			foreach ($this->fields as $key => $subField) {
				/** @var Field $subField */
				$subFieldValue = isset($values[$subField->name]) ? $values[$subField->name] : null;
				$data['fields'][$key] = $subField->content($subFieldValue);
			}
			$data['fields'] = implode("\n", $data['fields']);
		}

		return $view->content("field/{$this->type}/{$this->view}", $data);
	}

	final public function getData($value = null)
	{
		$data = array(
			'type' => $this->type,
			'view' => $this->view,
			'is_lock' => $this->isLock,
			'id' => $this->id,
			'name' => $this->isSubField
				? "{$this->prefix}[{$this->name}]"
				: "{$this->prefix}{$this->name}",
			'value' => null === $value ? $this->default : $this->normalize($value),
			'attributes' => array(),
			'label' => $this->label,
			'description' => $this->description,
			'contentBefore' => $this->contentBefore,
			'content' => $this->content,
			'contentAfter'  => $this->contentAfter,
			'options' => $this->options,
			'dependents' => array(),
			'fields' => null,
		);

		if ($this->isLock) {
			$data['name'] = null;
			$data['value'] = null;
		}

		foreach ($this->attributes as $attrName => $attrValue) {
			$attrValue = is_array($attrValue)
				? implode(' ', array_map('esc_attr', $attrValue))
				: esc_attr($attrValue);
			$data['attributes'][$attrName] = sprintf('%s="%s"', $attrName, $attrValue);
		}
		$data['attributes'] = implode(' ', $data['attributes']);

		foreach ($this->dependents as $value => $actions) {
			if (!is_array($actions)) {
				$data['dependents'][$value] = array();
				continue;
			}

			foreach ($actions as $action => $selectors) {
				if (is_array($selectors)) {
					$data['dependents'][$value][$action] = array_map('esc_attr', $selectors);
				} else {
					$data['dependents'][$value][$action] = array();
				}
			}
		}
		$data['dependents'] = json_encode($data['dependents']);

		return $data;
	}

	/**
	 * Save field value
	 *
	 * @param mixed $value
	 * @return void
	 */
	final public function save($value)
	{
		if (null === $this->name) {
			return;
		}

		if ($this->fields) {
			$normalizedValue = array();
			foreach ($this->fields as $subField) {
				$subValue = isset($value[$subField->name]) ? $value[$subField->name] : null;
				$normalizedValue[$subField->name] = $subField->normalize($subValue);
			}
		} else {
			$normalizedValue = $this->normalize($value);
		}

		update_post_meta($this->postId, "{$this->prefix}{$this->name}", $normalizedValue);
	}

	/**
	 * Generate random ID for field html element
	 *
	 * @return string
	 */
	final public static function randId()
	{
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet) - 1;
		$length = 10;
		$token = "";

		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[mt_rand(0, $max)];
		}

		return $token;
	}

	/**
	 * Get default field options
	 *
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return array();
	}

	/**
	 * Normalize field value
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($value)
	{
		if ($this->cbSanitize && is_callable($this->cbSanitize)) {
			$value = call_user_func($this->cbSanitize, $value);
		}
		return $value;
	}
}
