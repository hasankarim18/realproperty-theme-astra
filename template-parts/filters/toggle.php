<?php
$filter = $args['filter'];
?>
<label class="check-row">
    <input type="checkbox" name="<?php echo esc_attr($filter['param']); ?>" value="1" <?php checked(!empty($args['current'][$filter['param']])); ?>>
    <span><?php echo esc_html($filter['label']); ?></span>
</label>