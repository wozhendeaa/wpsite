<?php /* Widget options */
add_filter("wpqa_widget_options","himer_widget_options");
function himer_widget_options($options) {
	$options['about-widget'] = array(
		array(
			'name' => esc_html__('Title','himer'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Himer'
		),
		array(
			'name' => esc_html__('About text','himer'),
			'id'   => 'text',
			'type' => 'textarea',
			'std'  => '© '.date('Y').' Him&Her.'
		),
	);
	return $options;
}?>