<div class="brisum-custom-field">
	<?php if ($contentBefore) : ?>
		<div class="metabox content-before row">
			<div class="large-12 columns">
				<?php echo $contentBefore; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($content) : ?>
		<div class="metabox content row">
			<div class="large-12 columns">
				<?php echo $content; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php foreach ($fields as $field) : ?>
		<div id="wrap-field-<?php echo $field['id']; ?>"
		     class="row metabox wrap-field <?php echo $field['type']; ?> <?php echo "{$field['type']}-{$field['view']}"; ?>" >
			<?php if ($field['is_lock']) : ?>
				<div class="lock-overlay">
					<div class="lock-message">
						<?php echo apply_filters('lock_field_title', __('The field is locked')); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($field['contentBefore']) : ?>
				<div class="content-before small-12 columns">
					<?php echo $field['contentBefore']; ?>
				</div>
			<?php endif; ?>

			<?php if ($field['content']) : ?>
				<div class="content small-12 columns">
					<?php echo $field['content']; ?>
				</div>
			<?php endif; ?>

			<?php echo $field['field']; ?>

			<?php if ($field['contentAfter']) : ?>
				<div class="content-after small-12 columns">
					<?php echo $field['contentAfter']; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<?php if ($contentAfter) : ?>
		<div class="metabox content-after row">
			<div class=" large-12 columns">
				<?php echo $contentAfter; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
