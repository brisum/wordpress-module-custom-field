<?php

namespace Brisum\Wordpress\CustomField;

use Brisum\Wordpress\CustomField\Field\Field;
use Exception;
use WP_Post;

class MetaBox
{
	const STATE_OPEN = 'open';
	const STATE_CLOSE = 'close';

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @constructor
	 * @param array $settings
	 */
	public function __construct(array $settings) {
		$this->settings = array_merge(
			array(
				'blockView' => 'metabox',
				'active' => true,
				'order' => 0,
				'settings' => array(),
				'view' => 'default',
				'fields' => array()
			),
			$settings
		);
		// array_merge doesn't merge recursively, that's why merge settings separately
		$this->settings['settings'] = array_merge(
			array(
				'id' => '',
				'title' => '',
				'screen' => array(),
				'context' => 'advanced',
				'priority' => 'default',
				'callback_args' => null
			),
			$this->settings['settings']
		);

		if (!is_array($this->settings['settings']['screen'])) {
			$this->settings['settings']['screen'] = array($this->settings['settings']['screen']);
		}

		$customField = CustomField::getInstance();
		foreach ($this->settings['settings']['screen'] as $screen) {
			$customField->addScreen($screen);
		}

		if ($this->settings['active']) {
			switch ($this->settings['blockView']) {
				case 'raw':
					add_action('edit_form_after_editor', array($this, 'renderRawBox'), absint($this->settings['order']), 1);
					break;

				case 'metabox':
				default:
					add_action('add_meta_boxes', array($this, 'registerMetaBox'), absint($this->settings['order']));
			}
			add_action('user_register', array($this, 'setDefaultState'));
			add_action('save_post', array($this, 'save'));
		}
	}

	/**
	 * Register meta box
	 *
	 * @return void
	 */
	public function registerMetaBox()
	{
		add_meta_box(
			$this->settings['settings']['id'],
			$this->settings['settings']['title'],
			array($this, 'render'),
			$this->settings['settings']['screen'],
			$this->settings['settings']['context'],
			$this->settings['settings']['priority'],
			$this->settings['settings']['callback_args']
		);
	}

	/**
	 * Render raw box
	 *
	 * @return void
	 */
	public function renderRawBox(WP_Post $post)
	{
		if (!in_array($post->post_type, $this->settings['settings']['screen'])) {
			return;
		}

		?>
		<div class="brisum-custom-field">
			<div id="<?php echo $this->settings['settings']['id']; ?>" class="raw-box">
				<div class="raw-box-title">
					<?php echo $this->settings['settings']['title']; ?>
				</div>
				<div class="raw-box-inside">
					<?php $this->render($post); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Set default meta box state for new user
	 *
	 * @param int $userId
	 * @return void
	 */
	public function setDefaultState($userId)
	{
		foreach ($this->settings['settings']['screen'] as $screen) {
			$optionName = "closedpostboxes_{$screen}";
			$closedMetaBox = get_user_meta($userId, $optionName, true);
			$closedMetaBox = $closedMetaBox ? $closedMetaBox : array();

			if (self::STATE_OPEN === $this->settings['state']) {
				$keyMetaBox = array_search($this->settings['settings']['id'], $closedMetaBox);
				if (false !== $keyMetaBox) {
					unset($closedMetaBox[$keyMetaBox]);
				}
			} elseif (self::STATE_CLOSE == $this->settings['state']) {
				$closedMetaBox[] = $this->settings['settings']['id'];
				$closedMetaBox = array_unique($closedMetaBox);
			}

			update_user_meta($userId, $optionName, $closedMetaBox);
		}
	}

	/**
	 * Display meta box content
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function render(WP_Post $post)
	{
		$view = new View();
		$postMeta = get_post_meta($post->ID);
		$settings = $this->settings;
		$templatingFields = array('contentBefore', 'content', 'contentAfter');
		$nonce = '';

		foreach ($templatingFields as $templatingField) {
			if (!empty($settings[$templatingField])) {
				if (0 === strpos($settings[$templatingField], 'template::')) {
					$template = str_replace('template::', '', $settings[$templatingField]);
					$settings[$templatingField] = $view->content($template);
				}
			}
		}

		if (is_array($settings['fields'])) {
			foreach ($settings['fields'] as $key => $fieldSettings) {
				$field = FieldFactory::createField($post->ID, $fieldSettings);
				$fieldName = $field->get('prefix') && $field->get('name')
					? $field->get('prefix') . $field->get('name')
					: $field->get('name');
				$fieldValue = isset($postMeta[$fieldName])
					? reset($postMeta[$fieldName]) // get single meta
					: $field->get('default');
				$fieldValue = is_serialized($fieldValue) ? unserialize($fieldValue) : $fieldValue;

				$settings['fields'][$key] = $this->getFieldData($field, $fieldValue);
				$nonce .= $field->get('name');
			}
		}

		$nonceField = $this->createNonceField();
		$settings['fields'][] = $this->getFieldData($nonceField, wp_create_nonce($nonce));

		$view->render("metabox/{$this->settings['view']}", $settings);
	}

	/**
	 * Get field data
	 *
	 * @param Field $field
	 * @param mixed $value
	 * @return array
	 */
	protected function getFieldData(Field $field, $value)
	{
		return array(
			'type' => $field->get('type'),
			'view' => $field->get('view'),
			'is_lock' => $field->get('isLock'),
			'id' => $field->get('id'),
			'contentBefore' => $field->get('contentBefore'),
			'content' => $field->get('content'),
			'contentAfter' => $field->get('contentAfter'),
			'field' => $field->content($value),
		);
	}

	/**
	 * Create nonce field
	 *
	 * @return Field
	 * @throws Exception
	 */
	protected function createNonceField()
	{
		return FieldFactory::createField(
			0,
			array(
				'type' => 'hidden',
				'view' => 'default',
				'prefix' => "{$this->settings['settings']['id']}_",
				'name' => 'nonce',
			)
		);
	}

	/**
	 * Save meta box fields
	 *
	 * @param int $postId
	 * @return void
	 * @throws Exception
	 */
	public function save($postId) {
		if (defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		if ('post' !== strtolower($_SERVER['REQUEST_METHOD'])) {
			return;
		}

		$postType = isset($_POST['post_type']) ? $_POST['post_type'] : '';
		if (!in_array($postType, $this->settings['settings']['screen'])) {
			return;
		}

		if (!current_user_can('edit_post', $postId)) {
			header('HTTP/1.0 403 Forbidden');
			die("Access denied");
		}

		$nonceField = $this->createNonceField();
		$nonceName = $nonceField->get('prefix') . $nonceField->get('name');
		$nonceValue = isset($_POST[$nonceName]) ? $_POST[$nonceName] : null;
		$nonce = '';
		foreach ($this->settings['fields'] as $fieldConfig) {
			$nonce .= $fieldConfig['name'];
		}
		if(!wp_verify_nonce($nonceValue, $nonce)) {
			wp_nonce_ays(null);
		}

		foreach ($this->settings['fields'] as $fieldConfig) {
			if (!isset($_POST[$fieldConfig['name']])) {
				continue;
			}

			$field = FieldFactory::createField($postId, $fieldConfig);
			$fieldName = $field->get('prefix') . $field->get('name');
			$fieldValue = $_POST[$fieldName];

			$field->save($fieldValue);
		}
	}
}
