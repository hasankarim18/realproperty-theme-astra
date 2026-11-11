<?php
$filter = $args['filter'];
$options = pd_filter_options($args['key']);
$selected = isset($args['current'][$filter['param']]) ? (string) $args['current'][$filter['param']] : '';
$id = 'filter-' . $args['key'];

if (!$options) {
    return;
}
?>
<fieldset class="filter-group">
    <legend><?php echo esc_html($filter['label']); ?></legend>
    <label class="select-label"
        for="<?php echo esc_attr($id); ?>"><?php echo esc_html($filter['select_label']); ?></label>
    <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($filter['param']); ?>">
        <option value=""><?php echo esc_html($filter['all_label']); ?></option>
        <?php foreach ($options as $value => $label): ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected((string) $value, $selected); ?>>
                <?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>
</fieldset>