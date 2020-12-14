<?php

$options = array();


$options[] = array(
    'id'            => 'jnews_dark_mode_alert',
    'type'          => 'jnews-alert',
    'default'       => 'warning',
    'label'         => esc_html__('Attention','jnews'),
    'description'   => wp_kses(__('<ul>
                    <li><strong>You must add/drag Dark Mode in header builder to be able to use these options.</strong></li>
                    <li>Dark mode will be disabled while you are editing in page builder (Elementor/WPBakery).</li>
                    <li>If there is bug in dark mode, please do not hesitate to tell us.</li>
                </ul>','jnews'), wp_kses_allowed_html()),
);

$options[] = array(
    'id'            => 'jnews_dark_mode_global_set',
    'transport'     => 'postMessage',
    'default'       => false,
    'type'          => 'jnews-toggle',
    'label'         => esc_html__('Default Dark Mode','jnews'),
    'description'   => esc_html__('Set dark mode as default on your site.','jnews'),
);

$options[] = array(
    'id'            => 'jnews_dark_mode_user_set',
    'transport'     => 'postMessage',
    'default'       => true,
    'type'          => 'jnews-toggle',
    'label'         => esc_html__('Dark Mode for User/Reader','jnews'),
    'description'   => esc_html__('Allowing user/reader to switch dark mode, disabling this will hide Dark Mode toggle button.','jnews'),
);

return $options;
