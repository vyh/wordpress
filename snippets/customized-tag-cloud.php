/*  Customized Tag Cloud Widget by Nicki Hoffman (arestelle.net), 2018

    modified from WPBeginner example - https://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
*/

// Register and load the widget
function load_customized_tags_widget() {
    register_widget( 'customized_tags_widget' );
}
add_action( 'widgets_init', 'load_customized_tags_widget' );


// Creating the widget
class customized_tags_widget extends WP_Widget {
    // init
    function __construct() {
        parent::__construct(
            'customized_tags_widget',  // widget ID
            __('Customized Tag Cloud', 'monza'),  // widget name for UI
            array( 'description' => __( 'Tag cloud with customization options', 'monza' ), )
        );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];  // before and after widget arguments are defined by themes
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        // Get / use params for tag font size, taxonomy, and limit
        $min_size = $instance['min_size'] ? $instance['min_size'] : 10;
        $max_size = $instance['max_size'] ? $instance['max_size'] : 16;
        $tax = $instance['tax'] ? $instance['tax'] : 'post_tag';
        $number = $instance['number'] ? $instance['number'] : 42;
        $tag_args = array(
            'smallest'                  => $min_size,
            'largest'                   => $max_size,
            'taxonomy'                  => $tax,
            'number'                    => $number
        );
        wp_tag_cloud( $tag_args );

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $title = isset($instance['title']) ? $instance['title'] : __( 'Tags', 'monza' );
        $min_size = isset($instance['min_size']) ? $instance['min_size'] : 10;
        $max_size = isset($instance['max_size']) ? $instance['max_size'] : 16;
        $tax = isset($instance['tax']) ? $instance['tax'] : 'post_tag';
        $number = isset($instance['number']) ? $instance['number'] : 42;
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <label for="<?php echo $this->get_field_id( 'min_size' ); ?>"><?php _e( 'Minimum tag size:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'min_size' ); ?>" name="<?php echo $this->get_field_name( 'min_size' ); ?>" type="text" value="<?php echo esc_attr( $min_size ); ?>" />
            <label for="<?php echo $this->get_field_id( 'max_size' ); ?>"><?php _e( 'Maximum tag size:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max_size' ); ?>" name="<?php echo $this->get_field_name( 'max_size' ); ?>" type="text" value="<?php echo esc_attr( $max_size ); ?>" />
            <label for="<?php echo $this->get_field_id( 'tax' ); ?>"><?php _e( 'Taxonomy:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'tax' ); ?>" name="<?php echo $this->get_field_name( 'tax' ); ?>" type="text" value="<?php echo esc_attr( $tax ); ?>" />
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Max number of terms:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['min_size'] = ( ! empty( $new_instance['min_size'] ) ) ? strip_tags( $new_instance['min_size'] ) : '';
        $instance['max_size'] = ( ! empty( $new_instance['max_size'] ) ) ? strip_tags( $new_instance['max_size'] ) : '';
        $instance['tax'] = ( ! empty( $new_instance['tax'] ) ) ? strip_tags( $new_instance['tax'] ) : '';
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
        return $instance;
    }
}



