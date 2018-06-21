<div class="field image image-default small-12 columns">
	<?php if ($label) : ?>
		<label for="<?php echo $id; ?>">
			<?php echo $label; ?>
		</label>
	<?php endif; ?>

	<div class="row">
		<?php
			$leftColumns = empty($options['textBefore']) ? 0 : 3;
			$rightColumns = empty($options['textAfter']) ? 0 : 3;
			$centerColumns = 12 - $leftColumns - $rightColumns;
		?>

		<?php if (!empty($options['textBefore'])) : ?>
			<div class="columns small-<?php echo $leftColumns ?> text-before">
				<?php echo $options['textBefore']; ?>
			</div>
		<?php endif; ?>

		<div class="columns small-<?php echo $centerColumns; ?>">
			<input id="<?php echo $id; ?>" <?php echo $attributes; ?>
			       type="hidden" name="<?php echo $name; ?>"
			       value="<?php echo $value; ?>" />
		</div>
		<div class="columns small-12">
			<div class="thumb">
				<?php
					if ($value) {
						echo wp_get_attachment_image($value, 'thumbnail');
					}
				?>
			</div>
			<br>

			<p class="hide-if-no-js">
				<a href="javascript:void(0)" class="js-delete-image button" style="<?php echo $value ? '' : 'display:none' ?>">
					Удалить изображение
				</a>
				<a href="javascript:void(0)" class="js-add-image button" style="<?php echo $value ? 'display:none' : '' ?>"
				   data-choose="<?php esc_attr_e( 'Добавить изображение'); ?>"
				   data-update="<?php esc_attr_e( 'Добавить'); ?>"
				   data-delete="<?php esc_attr_e( 'Удалить изображение'); ?>"
				   data-text="<?php esc_attr_e( 'Удалить'); ?>">
					Добавить изображение
				</a>
			</p>
		</div>

		<?php if (!empty($options['textAfter'])) : ?>
			<div class="columns small-<?php echo $rightColumns ?> text-after">
				<?php echo $options['textAfter']; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ($description) : ?>
		<p class="help-text"><?php echo $description; ?></p>
	<?php endif; ?>
</div>
