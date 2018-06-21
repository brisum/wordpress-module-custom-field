<?php

namespace Brisum\Wordpress\CustomField;

use Brisum\Lib\Storage\ArrayStorage;

class CustomField
{
	/**
	 * @var CustomField
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $screens = [];

	/**
	 * @return CustomField
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public function init()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_filter('admin_body_class', array($this, 'adminBodyClass'));
	}

	/**
	 * @return void
	 */
	public function enqueueScripts()
	{
		$screen = get_current_screen();
		if ('post' !== $screen->base) {
			return;
		}
		if (!in_array($screen->post_type, $this->screens)) {
			return;
		}

		wp_enqueue_style('brisum_custom_field_app_css', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/core/css/app.css', array(), 1);
		wp_enqueue_style('colorpickersliders_prettify_css', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/prettify/prettify.css', array('brisum_custom_field_app_css'), 1);
		wp_enqueue_style('colorpickersliders_colorpicker_css', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/jquery-colorpickersliders/jquery.colorpickersliders.css', array('brisum_custom_field_app_css'), 1);

		wp_enqueue_media();
		wp_enqueue_script('brisum_custom_field_foundation_js', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/foundation/foundation.min.js', array('jquery'), false, true);
		wp_enqueue_script('colorpickersliders_prettify_js', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/prettify/prettify.js', array('brisum_custom_field_foundation_js'), false, true);
		wp_enqueue_script('colorpickersliders_tinycolor_js', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/tinycolor/tinycolor.js', array('brisum_custom_field_foundation_js'), false, true);
		wp_enqueue_script('colorpickersliders_colorpickersliders_js', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/jquery-colorpickersliders/jquery.colorpickersliders.js', array('brisum_custom_field_foundation_js'), false, true);
		wp_enqueue_script('brisum_custom_field_app_js', BRISUM_CUSTOM_FIELD_URL . '/asset/dist/core/js/app.js', array('brisum_custom_field_foundation_js'), false, true);
	}

	/**
	 * @param string $classes
	 * @return string
	 */
	public function adminBodyClass($classes)
	{
		return $classes . ' ' . BRISUM_CUSTOM_FIELD_BODY_CLASS;
	}

	/**
	 * @param string $screen
	 * @return void
	 */
	public function addScreen($screen)
	{
		$this->screens[] = $screen;
	}
}
