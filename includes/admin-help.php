<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the RS Manager Help submenu.
 */
function rsm_register_help_submenu() {
    add_submenu_page(
        'rsm-manager',
        esc_html__( 'RS Manager Help', 'race-series-manager' ),
        esc_html__( 'Help', 'race-series-manager' ),
        'edit_posts',
        'rsm-help',
        'rsm_render_help_page'
    );
}
add_action( 'admin_menu', 'rsm_register_help_submenu' );

/**
 * Convert a subset of Markdown formatting to HTML for display in the admin help page.
 */
function rsm_markdown_to_html( $markdown ) {
    $content = str_replace( array( "\r\n", "\r" ), "\n", $markdown );

    // Preserve fenced code blocks while other formatting runs.
    $code_blocks = array();
    $content     = preg_replace_callback(
        '/```(.*?)```/s',
        function ( $matches ) use ( &$code_blocks ) {
            $placeholder                        = '%%RSM_CODE_' . count( $code_blocks ) . '%%';
            $code_blocks[ $placeholder ] = '<pre><code>' . esc_html( trim( $matches[1] ) ) . '</code></pre>';
            return $placeholder;
        },
        $content
    );

    // Inline styles.
    $content = preg_replace_callback(
        '/\*\*(.+?)\*\*/s',
        function ( $matches ) {
            return '<strong>' . esc_html( $matches[1] ) . '</strong>';
        },
        $content
    );

    $content = preg_replace_callback(
        '/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s',
        function ( $matches ) {
            return '<em>' . esc_html( $matches[1] ) . '</em>';
        },
        $content
    );

    $content = preg_replace_callback(
        '/`([^`]+)`/',
        function ( $matches ) {
            return '<code>' . esc_html( $matches[1] ) . '</code>';
        },
        $content
    );

    // Headings.
    for ( $level = 6; $level >= 2; $level-- ) {
        $pattern = '/^' . str_repeat( '#', $level ) . '\s+(.+)$/m';
        $content = preg_replace_callback(
            $pattern,
            function ( $matches ) use ( $level ) {
                return sprintf( '<h%d>%s</h%d>', $level, esc_html( trim( $matches[1] ) ), $level );
            },
            $content
        );
    }

    $content = preg_replace_callback(
        '/^#\s+(.+)$/m',
        function ( $matches ) {
            return '<h1>' . esc_html( trim( $matches[1] ) ) . '</h1>';
        },
        $content
    );

    // Lists.
    $content = preg_replace_callback(
        '/(^- .+(?:\n- .+)*)/m',
        function ( $matches ) {
            $lines = explode( "\n", trim( $matches[1] ) );
            $items = '';
            foreach ( $lines as $line ) {
                $items .= '<li>' . esc_html( trim( substr( $line, 2 ) ) ) . '</li>';
            }
            return '<ul>' . $items . '</ul>';
        },
        $content
    );

    $content = preg_replace_callback(
        '/(^\d+\. .+(?:\n\d+\. .+)*)/m',
        function ( $matches ) {
            $lines = explode( "\n", trim( $matches[1] ) );
            $items = '';
            foreach ( $lines as $line ) {
                $items .= '<li>' . esc_html( trim( preg_replace( '/^\d+\.\s+/', '', $line ) ) ) . '</li>';
            }
            return '<ol>' . $items . '</ol>';
        },
        $content
    );

    $content = wpautop( $content );

    if ( $code_blocks ) {
        foreach ( $code_blocks as $placeholder => $code_html ) {
            $content = str_replace( $placeholder, $code_html, $content );
        }
    }

    return wp_kses_post( $content );
}

/**
 * Render the backend help page populated from ADMIN_HELP.md.
 */
function rsm_render_help_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    $help_file = RSM_PLUGIN_DIR . 'ADMIN_HELP.md';
    $content   = '';

    if ( file_exists( $help_file ) && is_readable( $help_file ) ) {
        $markdown = file_get_contents( $help_file );
        $content  = rsm_markdown_to_html( $markdown );
    }
    ?>
    <div class="wrap rsm-help-wrap">
        <h1><?php esc_html_e( 'RS Manager Help', 'race-series-manager' ); ?></h1>
        <?php if ( $content ) : ?>
            <div class="rsm-help-content">
                <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        <?php else : ?>
            <div class="notice notice-warning inline">
                <p><?php esc_html_e( 'The admin help guide could not be loaded. Make sure ADMIN_HELP.md exists in the plugin directory.', 'race-series-manager' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <style>
        .rsm-help-content h1,
        .rsm-help-content h2,
        .rsm-help-content h3,
        .rsm-help-content h4,
        .rsm-help-content h5,
        .rsm-help-content h6 {
            margin-top: 1.5em;
        }

        .rsm-help-content ul,
        .rsm-help-content ol {
            margin-left: 1.4em;
        }

        .rsm-help-content code {
            background: #f6f7f7;
            padding: 0.15em 0.35em;
            border-radius: 3px;
        }

        .rsm-help-content pre {
            background: #f6f7f7;
            padding: 12px;
            border-radius: 4px;
            overflow: auto;
            white-space: pre-wrap;
        }
    </style>
    <?php
}
