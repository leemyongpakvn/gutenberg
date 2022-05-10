<?php
/**
 * Server-side rendering of the `core/comment-date` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-date` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's date.
 */
function render_block_core_comment_date( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	if ( 0 === $block->context['commentId'] ) {
		// TODO: Translate format to JS-style.
		$formatted_date = <<<END
		\${ context.timestamp.toLocaleString( "en", {
			year: 'numeric', month: 'long', day: 'numeric',
			hour: 'numeric', minute: 'numeric'
		} ) }
		END;
		$timestamp = '${ context.timestamp.toISOString() }';
		$wrapper_attributes = get_block_wrapper_attributes();
	} else {
		$comment = get_comment( $block->context['commentId'] );
		if ( empty( $comment ) ) {
			return '';
		}

		$classes = '';
		if ( isset( $attributes['fontSize'] ) ) {
			$classes .= 'has-' . esc_attr( $attributes['fontSize'] ) . '-font-size';
		}

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );
		$formatted_date     = get_comment_date(
			isset( $attributes['format'] ) ? $attributes['format'] : '',
			$comment
		);
		$link               = get_comment_link( $comment );

		if ( ! empty( $attributes['isLink'] ) ) {
			$formatted_date = sprintf( '<a href="%1s">%2s</a>', esc_url( $link ), $formatted_date );
		}
		
		$timestamp = esc_attr( get_comment_date( 'c', $comment ) );
	}

	return sprintf(
		'<div %1$s><time datetime="%2$s">%3$s</time></div>',
		$wrapper_attributes,
		$timestamp,
		$formatted_date
	);
}

/**
 * Registers the `core/comment-date` block on the server.
 */
function register_block_core_comment_date() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-date',
		array(
			'render_callback' => 'render_block_core_comment_date',
		)
	);
}
add_action( 'init', 'register_block_core_comment_date' );
