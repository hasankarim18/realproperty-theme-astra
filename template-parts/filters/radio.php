<?php
$filter = $args['filter'];
$options = pd_filter_options($args['key']);
$selected = isset($args['current'][$filter['param']]) ? (string) $args['current'][$filter['param']] : '';

if (!$options) {
    return;
}
?>
<fieldset class="filter-group">
    <legend><?php echo esc_html($filter['label']); ?></legend>
    <div class="bedroom-options">
        <?php foreach ($options as $value => $label): ?>
            <label>
                <input type="radio" name="<?php echo esc_attr($filter['param']); ?>"
                    value="<?php echo esc_attr($value); ?>" <?php checked((string) $value === $selected); ?>>
                <span><?php echo esc_html($label); ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</fieldset>