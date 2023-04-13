<?php

// If uninstall not called from WordPress, then exit.
if (!defined( 'WP_UNINSTALL_PLUGIN')) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Delete all fields values
 */
$repository = \Carbon_Fields\Carbon_Fields::resolve('container_repository');
$containers = $repository->get_containers();

foreach ($containers as $container) {
    if ($container->get_id() !== 'carbon_fields_container_tik_tok') {
        continue;
    }

    $fields = $container->get_fields();
    foreach ($fields as $field) {
        $field->delete();
    }
}

/**
 * Delete auth secret
 */
delete_option('tik_tok_feed_auth_secret');