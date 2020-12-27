<?php
/**
 * Customizer Control: Image.
 *
 * Creates a text
 *
 * @author Jegstudio
 * @since 1.0.0
 * @package jeg-framework
 */

namespace Jeg\Form\Field;

/**
 * Image control
 */
class Image extends Field_Abstract {

	/**
	 * Form Text Template
	 *
	 * @var string
	 */
	protected $type = 'image';

	/**
	 * An Underscore (JS) template for this control's content
	 */
	public function js_template() {
		?>
		<div class="widget-wrapper image-control type-image" data-field="{{ data.fieldID }}">
			<div class="widget-left">
				<label for="{{ data.fieldID }}">{{{ data.title }}}</label>
			</div>
			<div class="widget-right">
				<div class="image-content">
					<# var showImageClass = ( '' === data.value ) ? 'hide-image' : '' #>
					<div class="image-wrapper {{ showImageClass }}">
						<img src="{{ data.imageUrl }}">
					</div>
					<# var addButtonClass = ( '' === data.value ) ? '' : 'hide-button'; #>
					<input type="button" class="button-image-text add-button button {{ addButtonClass }}" value="<?php esc_html_e( 'Add Image', 'jeg' ); ?>">
					<# var removeButtonClass = ( '' === data.value ) ? 'hide-button' : ''; #>
					<input type="button" class="button-image-text remove-button button {{ removeButtonClass }}" value="<?php esc_html_e( 'Remove Image', 'jeg' ); ?>">
					<input type="hidden" class="image-input" id="{{ data.fieldID }}" name="{{ data.fieldName }}" value="{{ data.value }}" />
				</div>
				<i>{{{ data.description }}}</i>
			</div>
		</div>
		<?php
	}
}
