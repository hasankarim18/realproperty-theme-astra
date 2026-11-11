<?php
$filter = $args['filter'];
$options = pd_filter_options($args['key']);
$selected = isset($args['current'][$filter['param']]) ? array_map('strval', (array) $args['current'][$filter['param']]) : array();

if (!$options) {
    return;
}
?>
<fieldset class="filter-group">
    <legend><?php echo esc_html($filter['label']); ?></legend>
    <?php foreach ($options as $value => $label): ?>
        <label class="check-row">
            <input type="checkbox" name="<?php echo esc_attr($filter['param']); ?>[]"
                value="<?php echo esc_attr($value); ?>" <?php checked(in_array((string) $value, $selected, true)); ?>>
            <span><?php echo esc_html($label); ?></span>
        </label>
    <?php endforeach; ?>
</fieldset>