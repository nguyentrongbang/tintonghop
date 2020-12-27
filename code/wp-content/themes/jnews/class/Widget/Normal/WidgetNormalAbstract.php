<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Widget\Normal;

use Jeg\Util\Sanitize;
use JNews\Widget\AdditionalWidget;
use JNews\Widget\WidgetAbstract;
use Jeg\Form\Form_Widget;

abstract class WidgetNormalAbstract extends WidgetAbstract {
	/**
	 * @var NormalWidgetInterface
	 */
	protected $widget_instance;

	public function __construct() {
		$base_name = $this->get_base_name();

		if ( is_admin() ) {
			parent::__construct( $base_name, $this->get_module_name(), array(
				'description' => $this->get_module_name()
			) );
		} else {
			parent::__construct( $base_name, null, null );
		}
	}

	public function get_module_name() {
		$element = get_class( $this );
		$text    = explode( '_', $element );

		return "JNews - " . ucfirst( implode( ' ', $text ) );
	}

	public function get_base_name() {
		$base_name = str_replace( '_Widget', '', get_class( $this ) );

		return 'jnews_' . strtolower( $base_name );
	}

	/**
	 * @return NormalWidgetInterface
	 */
	public function get_widget_instance() {
		if ( ! $this->widget_instance ) {
			$widgetClass           = $this->get_widget_class();
			$this->widget_instance = new $widgetClass();
		}

		return $this->widget_instance;
	}

	public function get_widget_class() {
		$class          = str_replace( '_', '', get_class( $this ) );
		$class_instance = "JNews\\Widget\\Normal\\Element\\" . $class;

		return apply_filters( 'jnews_widget_class_instance', $class_instance, $class );
	}

	public function form( $instance ) {
		if ( ! is_customize_preview() ) {
			$id       = $this->get_field_id( 'widget_news_element' );
			$segments = $this->prepare_segments();
			$fields   = $this->prepare_fields( $instance );

			$additional_instance = AdditionalWidget::getInstance();
			$additional_field    = $additional_instance->prepare_fields( $this, $instance );
			$additional_segment  = $additional_instance->prepare_segments();

			if ( class_exists( 'Jeg\Form\Form_Widget' ) ) {
				Form_Widget::render_form( $id, array_merge( $segments, $additional_segment ), array_merge( $fields, $additional_field ) );
			}
		}
	}

	public function get_value( $id, $value, $default ) {
		if ( isset( $value[ $id ] ) ) {
			return $value[ $id ];
		} else {
			return $default;
		}
	}

	public function prepare_segments() {
		$segments = array();
		$priority = 1;
		$options  = $this->get_widget_instance()->get_options();

		foreach ( $options as $option ) {
			if ( ! isset( $option['group'] ) || empty( $option['group'] ) ) {
				$option['group'] = $this->get_default_group();
			}

			$id = sanitize_title_with_dashes( $option['group'] );

			if ( ! isset( $segments[ $id ] ) ) {
				$segments[ $id ] = array(
					'id'       => $id,
					'type'     => 'default',
					'name'     => $option['group'],
					'priority' => $priority ++,
				);
			}
		}

		return $segments;
	}

	public function prepare_fields( $instance ) {
		$setting = array();
		$fields  = $this->get_widget_instance()->get_options();

		foreach ( $fields as $key => $field ) {

			$setting[ $key ]              = array();
			$setting[ $key ]['id']        = $key;
			$setting[ $key ]['fieldID']   = $this->get_field_id( $key );
			$setting[ $key ]['fieldName'] = $this->get_field_name( $key );
			$setting[ $key ]['type']      = $field['type'];

			$setting[ $key ]['title']       = isset( $field['title'] ) ? $field['title'] : '';
			$setting[ $key ]['description'] = isset( $field['desc'] ) ? $field['desc'] : '';
			$setting[ $key ]['segment']     = isset( $field['group'] ) ? sanitize_title_with_dashes( $field['group'] ) : sanitize_title_with_dashes( $this->get_default_group() );
			$setting[ $key ]['default']     = isset( $field['default'] ) ? $field['default'] : '';
			$setting[ $key ]['priority']    = isset( $field['priority'] ) ? $field['priority'] : 10;
			$setting[ $key ]['options']     = isset( $field['options'] ) ? $field['options'] : array();
			$setting[ $key ]['dependency']  = isset( $field['dependency'] ) ? $field['dependency'] : '';
			$setting[ $key ]['multiple']    = isset( $field['multiple'] ) ? $field['multiple'] : 1;
			$setting[ $key ]['ajax']        = isset( $field['ajax'] ) ? $field['ajax'] : '';
			$setting[ $key ]['nonce']       = isset( $field['nonce'] ) ? $field['nonce'] : '';
			$setting[ $key ]['choices']     = isset( $field['choices'] ) ? $field['choices'] : '';
			$setting[ $key ]['value']       = $this->get_value( $key, $instance, $setting[ $key ]['default'] );
			$setting[ $key ]['fields']      = isset( $field['fields'] ) ? $field['fields'] : array();
			$setting[ $key ]['row_label']   = isset( $field['row_label'] ) ? $field['row_label'] : array();

			// only for image type
			if ( 'image' === $setting[ $key ]['type'] ) {
				$image = wp_get_attachment_image_src( $setting[ $key ]['value'], 'full' );
				if ( isset( $image[0] ) ) {
					$setting[ $key ]['imageUrl'] = $image[0];
				}
			}
		}

		return $setting;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		if ( class_exists( 'Jeg\Util\Sanitize' ) ) {
			$sanitizeClass = Sanitize::get_instance();

			foreach ( $this->get_widget_instance()->get_options() as $key => $field ) :

				if ( isset( $field['type'] ) ) {
					switch ( $field['type'] ) {
						case 'textarea' :
	                        if ( isset( $new_instance[ $key ] ) ) {
	                            $instance[$key] = wp_kses($new_instance[$key], wp_kses_allowed_html());
	                        }
							break;
						default :
							if ( isset( $new_instance[ $key ] ) ) {
								$instance[ $key ] = $sanitizeClass->sanitize_input( $new_instance[ $key ] );
							}
							break;
					}
				}

			endforeach;
		}

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : "" );

		echo jnews_sanitize_output( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo jnews_sanitize_output( $args['before_title'] ) . esc_html( $title ) . jnews_sanitize_output( $args['after_title'] );
		}

		$widget_instance = $this->get_widget_instance();
		$widget_instance->render_widget( $instance );

		echo jnews_sanitize_output( $args['after_widget'] );
	}
}
