<div class="field small-12 columns">
	<?php if ($label) : ?>
		<label>
			<?php echo $label; ?>
		</label>
	<?php endif; ?>
</div>

<div class="field small-4 columns">
	<input id="<?php echo $id; ?>" <?php echo $attributes; ?>
	       type="text" name="<?php echo $name; ?>"
	       value="<?php echo $value; ?>"
	       data-colorpicker
	       data-color-format="<?php echo $options['color-format']; ?>"
	       data-options='<?php echo json_encode($options['colorpicker']); ?>' >
</div>

<div class="content small-12 columns">
	<?php if ($description) : ?>
		<p class="help-text"><?php echo $description; ?></p>
	<?php endif; ?>
</div>
