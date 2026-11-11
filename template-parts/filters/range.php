<?php
$filter = $args['filter'];
$min = isset($args['current']['min_price']) ? $args['current']['min_price'] : '';
$max = isset($args['current']['max_price']) ? $args['current']['max_price'] : '';
?>
<fieldset class="filter-group">
    <legend><?php echo esc_html($filter['label']); ?></legend>
    <div class="price-inputs">
        <label>
            <span><?php esc_html_e('Minimum', 'property-directory'); ?></span>
            <input type="number" name="min_price" min="0" step="1" inputmode="numeric" placeholder="৳ 0"
                value="<?php echo esc_attr($min); ?>">
        </label>
        <span class="range-dash">–</span>
        <label>
            <span><?php esc_html_e('Maximum', 'property-directory'); ?></span>
            <input type="number" name="max_price" min="0" step="1" inputmode="numeric"
                placeholder="<?php esc_attr_e('Any', 'property-directory'); ?>"
                value="<?php echo esc_attr($max); ?>">
        </label>
    </div>
</fieldset>