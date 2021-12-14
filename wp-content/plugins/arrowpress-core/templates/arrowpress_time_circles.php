<?php
	$output = $el_class ='';
	extract(shortcode_atts(array(
		'times' => '2019-01-01',   
		'timer_bgcolor' => '#fff',   
		'line_bgcolor' => '#3a90f4',   
		'text_bgcolor' => '#fff',   
		'number_bgcolor' => '#fff',   
		'times_border' => 0.15,   
		'line_border' => 0.03,   
		'text_size' => 0.11,   
		'number_size' => 0.24,   
		'days' => esc_html__('days','arrowpress-core'),   
		'hours' => esc_html__('hours','arrowpress-core'),   
		'mins' => esc_html__('mins','arrowpress-core'),   
		'secs' => esc_html__('secs','arrowpress-core'),   
		'el_class' => ''
	), $atts));
	$id_times =  'time_'.wp_rand();
	$el_class = arrowpress_shortcode_extract_class($el_class);
	$output = '<div class="times-container' . $el_class . '"';
	$output .= '>';
	ob_start();
?>
	<div id="<?php echo esc_attr($id_times); ?>" class="time-circles" data-date="<?php echo esc_attr($times); ?>" style="width: 500px; height: 125px;"></div>
	<script type="text/javascript">  
		jQuery('document').ready(function() {
			'use strict';
			jQuery("#<?php echo esc_attr($id_times); ?>").TimeCircles({
				circle_bg_color: "<?php echo esc_attr($timer_bgcolor); ?>",
				fg_width: <?php echo esc_attr($line_border); ?>,
				bg_width: <?php echo esc_attr($times_border); ?>,
				text_size: <?php echo esc_attr($text_size); ?>,
				number_size: <?php echo esc_attr($number_size); ?>,
				time: {
					Days: {
						show: true,
						text: "<?php echo esc_attr($days); ?>",
						color: "<?php echo esc_attr($line_bgcolor); ?>"
					},
					Hours: {
						show: true,
						text: "<?php echo esc_attr($hours); ?>",
						color: "<?php echo esc_attr($line_bgcolor); ?>"
					},
					Minutes: {
						show: true,
						text: "<?php echo esc_attr($mins); ?>",
						color: "<?php echo esc_attr($line_bgcolor); ?>"
					},
					Seconds: {
						show: true,
						text: "<?php echo esc_attr($secs); ?>",
						color: "<?php echo esc_attr($line_bgcolor); ?>"
					}
				}
			});
		});
	</script>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_time_circles') . "\n";

echo $output;


wp_reset_postdata();
