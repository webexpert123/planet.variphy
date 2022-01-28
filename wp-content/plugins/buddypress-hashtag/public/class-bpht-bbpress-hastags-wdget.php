<?php


class BPHT_Bbpress_Hashtag_Widget extends WP_Widget
{
    public function __construct( $id = NULL, $name = NULL, $args= NULL ) {

        $id     = ( NULL !== $id )  ? $id   : 'BPHT_Bbpress_Hashtag_Widget';
        $name   = ( NULL !== $name )? $name : __('Bbpress Hashtags', 'buddypress-hashtags');
        $args   = ( NULL !== $args )? $args : array('description' => __('Bbpress Hashtags Widget', 'buddypress-hashtags'),);

        parent::__construct(
            $id,
            $name,
            $args
        );
    }
	
	public function shuffle_assoc($list) { 
	
		if (!is_array($list)) return $list; 

		$keys = array_keys($list); 
		shuffle($keys); 
		$random = array(); 
		foreach ($keys as $key) { 
			$random[$key] = $list[$key]; 
		}
		return $random; 
	} 
	
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance ) {
        $instance = apply_filters('bpht_bbpress_hashtag_widget_instance', $instance);

        if(!isset($instance['sortby'])) {
            $instance['sortby']=0;
        }

        if(!isset($instance['sortorder'])) {
            $instance['sortorder']=0;
        }

        if(!isset($instance['displaystyle'])) {
            $instance['displaystyle']=0;
        }
        echo $args['before_widget'];
        ?>

        <div>
            <div>
                <?php
                if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
                }
                ?>
            </div>
            <?php

            if(!isset($instance['limit'])) {
                $instance['limit'] = 12;
            }

            $limit = $instance['limit'];

            global $wpdb;
            $table_name = $wpdb->prefix . 'bpht_hashtags';
            $hashtags = array();
            if($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) {
                $_hashtags = $wpdb->get_results("SELECT * FROM $table_name WHERE ht_type IN ('bbpress') ORDER BY ht_count DESC LIMIT $limit");
                if( $_hashtags ) {
                    foreach ( $_hashtags as $key => $ht_data) {
                        $hashtags[$ht_data->ht_name] = array(
                            'ht_count' => $ht_data->ht_count,
                            'ht_last_count' => $ht_data->ht_last_count,
                            'ht_type' => $ht_data->ht_type
                        );
                    }
                }
            }

			$max = 10;
			$min = 1;
			$fontMin = 14;
			$fontMax = 28;

            if ( count($hashtags) && is_array( $hashtags ))
            {
                $result = array();

                if(0==$instance['sortby']) {
                    if(0==$instance['sortorder']) {
                        //ksort($hashtags);
						ksort($hashtags,SORT_STRING | SORT_FLAG_CASE);
                    }

                    if(1==$instance['sortorder']) {
                        //krsort($hashtags);
						krsort($hashtags, SORT_STRING | SORT_FLAG_CASE);
                    }
                } elseif(1==$instance['sortby']) {
                    if(0==$instance['sortorder']) {
                        asort($hashtags);
                    }

                    if(1==$instance['sortorder']) {
                        arsort($hashtags);
                    }
                } elseif(2==$instance['sortby']) {
					$hashtags = $this->shuffle_assoc($hashtags);
                }
                $wrapper = '';

                if( 1==$instance['displaystyle'] ) {
                    $wrapper='bpht-hashtags-wrapper-list';
                }elseif( 0==$instance['displaystyle'] ){
                    $wrapper='bpht-hashtags-wrapper-cloud';
                }

                ?>
                <div>
                    <div class="bpht-widget--hashtags">
                        <div class="bpht-hashtags-wrapper <?php echo $wrapper;?>">
                            <?php

                            foreach($hashtags as $name => $hash_data) {

                                $percentage = 100; // default percentage if tags have no counts (the max is 0)
                                if($max > 0) {
                                    $percentage = round($hash_data['ht_count'] / $max * 10) * 10;
                                }

                                $size = 'bpht-hashtag--box bpht-hashtag--size'.$percentage;

                                if(1==$instance['displaystyle']) {
                                    $size='';
                                }

                                if(2==$instance['displaystyle']) {
                                    $size = 'bpht-hashtag--size'.$percentage;
                                }
								
								$style = "";
								if ( $instance['sortby'] == 2 ) {
									 $fontsize = ( $hash_data['ht_count'] == $min ) ? $fontMin  : ( $hash_data['ht_count'] / $max) * ($fontMax - $fontMin) + $fontMin;
									 $style = 'style="font-size:'. $fontsize .'px;"';
								}

                                ?>
                                <?php
                                $site_url = trailingslashit( get_bloginfo('url') );
                                $forum_slug = get_option('_bbp_root_slug');
                                $search_slug = get_option('_bbp_search_slug');
                                echo '<div data-size="'.$size.'" ' . $style. '>';
                                echo ' <a href="'. $site_url . $forum_slug . '/' . $search_slug . '/?bbp_search=%23' . htmlspecialchars( $name ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $name ) . '">#'. htmlspecialchars( $name ) .'</a>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                    <div>
                        <span class='bpht-text--muted'><?php echo __('No hashtags', 'buddypress-hashtags');?></span>
                    </div>
                <?php } ?>
            </div>
            <?php
            echo $args['after_widget'];
    }

    /**
     * Outputs the admin options form
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $limit_options = array();

        for($i=1; $i<=50; $i++) {
            $limit_options[]=$i;
        }

        $instance['fields'] = array(
            // general
            'limit'         => TRUE,
            'limit_options' => $limit_options,
            'title'         => TRUE,
            'integrated'    => FALSE,
            'position'      => FALSE,
            'hideempty'     => FALSE,
        );

        if (!isset($instance['title'])) {
            $instance['title'] = __('Bbpress Hashtags', 'buddypress-hashtags');
        }

        $this->instance = $instance;

        $settings =  apply_filters('bpht_bp_hashtag_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));
        echo $settings['html'];

        $title = !empty($instance['title']) ? $instance['title'] : 'BuddyPress Hashtags';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 12;
       $sortby = ( $instance['sortby'] != '' || $instance['sortby'] == 0 ) ? $instance['sortby'] : 2;
        $sortorder = !empty($instance['sortorder']) ? $instance['sortorder'] : 0;
        $displaystyle= !empty($instance['displaystyle']) ? $instance['displaystyle'] : 0;
        ?>

        <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:','buddypress-hashtags'); ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>"
            name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'buddypress-hashtags'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>">
                <?php
                $options = array();
                for ($i = 1; $i <= 100; $i++) {
                    if ($i <= 10 || $i % 2 == 0) {
                        $options[] = $i;
                    }
                }

                if(!empty($instance['fields']['limit_options'])) {
                    $options = $instance['fields']['limit_options'];
                }

                foreach($options as $option)
                {
                    ?>
                    <option value="<?php echo $option;?>" <?php if($option==$limit) echo " selected ";?> ><?php echo $option;?></option>
                    <?php
                }
                ?>
            </select>
        </p>
        <p>
            <select name="<?php echo $this->get_field_name('displaystyle');?>" id="<?php echo $this->get_field_id('displaystyle');?>">
                <option value="0" <?php if (0 === $displaystyle ) { echo ' selected '; }?>><?php echo __('Cloud','buddypress-hashtags');?></option>
                <option value="1" <?php if (1 === $displaystyle ) { echo ' selected '; }?>><?php echo __('List','buddypress-hashtags');?></option>
            </select>

            <select name="<?php echo $this->get_field_name('sortby');?>" id="<?php echo $this->get_field_id('sortby');?>">
                <option value="0" <?php if (0 === $sortby ) { echo ' selected '; }?>><?php echo __('Sorted by name','buddypress-hashtags');?></option>
                <option value="1" <?php if (1 === $sortby ) { echo ' selected '; }?>><?php echo __('Sorted by size','buddypress-hashtags');?></option>
				<option value="2" <?php if (2 === $sortby ) { echo ' selected '; }?>><?php echo __('Random','buddypress-hashtags');?></option>
            </select>

            <select name="<?php echo $this->get_field_name('sortorder');?>" id="<?php echo $this->get_field_id('sortorder');?>">
                <option value="0" <?php if (0 === $sortorder ) { echo ' selected '; }?>><?php echo __('&uarr;','buddypress-hashtags');?></option>
                <option value="1" <?php if (1 === $sortorder ) { echo ' selected '; }?>><?php echo __('&darr;','buddypress-hashtags');?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['limit']       = (int) $new_instance['limit'];

        $instance['sortby']      = (int) $new_instance['sortby'];
        $instance['sortorder']   = (int) $new_instance['sortorder'];
        $instance['displaystyle']   = (int) $new_instance['displaystyle'];

        return $instance;
    }
}
