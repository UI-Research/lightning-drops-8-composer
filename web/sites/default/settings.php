<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => dirname(DRUPAL_ROOT) . '/config/sync',
);


/**
 * Pantheon-specific settings
 */
if (defined('PANTHEON_ENVIRONMENT')) {
  $variables = array(
    'https' => true,
//    'domains' =>
//      array (
//        'canonical' => 'next50.urban.org',
//        'synonyms' =>
//          array (
//            0 => 'live-next50.pantheonsite.io',
//          ),
//      ),
    'redis' => FALSE,
  );

  // If necessary, force redirect in to https
  if (isset($variables)) {
    if (array_key_exists('https', $variables) && $variables['https']) {
      if (!$cli && $_SERVER['HTTPS'] === 'OFF') {
        if (!isset($_SERVER['HTTP_X_SSL']) || (isset($_SERVER['HTTP_X_SSL']) && $_SERVER['HTTP_X_SSL'] != 'ON')) {
          header('HTTP/1.0 301 Moved Permanently');
          header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
          exit();
        }
      }
    }
  }

  if (array_key_exists('redis', $variables) && $variables['redis']) {
    // Configure Redis on Pantheon
    if (defined('PANTHEON_ENVIRONMENT')) {
      // Include the Redis services.yml file. Adjust the path if you installed to a contrib or other subdirectory.
      $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';

      //phpredis is built into the Pantheon application container.
      $settings['redis.connection']['interface'] = 'PhpRedis';
      // These are dynamic variables handled by Pantheon.
      $settings['redis.connection']['host']      = $_ENV['CACHE_HOST'];
      $settings['redis.connection']['port']      = $_ENV['CACHE_PORT'];
      $settings['redis.connection']['password']  = $_ENV['CACHE_PASSWORD'];

      $settings['cache']['default'] = 'cache.backend.redis'; // Use Redis as the default cache.
      $settings['cache_prefix']['default'] = 'pantheon-redis';

      // Always set the fast backend for bootstrap, discover and config, otherwise this gets lost when redis is enabled.
      $settings['cache']['bins']['bootstrap'] = 'cache.backend.chainedfast';
      $settings['cache']['bins']['discovery'] = 'cache.backend.chainedfast';
      $settings['cache']['bins']['config']    = 'cache.backend.chainedfast';

      // Set Redis to not get the cache_form (no performance difference).
      $settings['cache']['bins']['form']      = 'cache.backend.database';
    }
  }

  // Config Split dev modules enabled by default.
  // $config['config_split.config_split.dev']['status'] = TRUE;

  if (PANTHEON_ENVIRONMENT != 'live') {
    // Place for settings for the non-live environment
    $conf['reroute_email_enable'] = 1;

    $config['system.performance']['cache']['page']['use_internal'] = FALSE;
    $config['system.performance']['cache']['page']['max_age'] = 0;
    $config['system.performance']['css']['preprocess'] = FALSE;
    $config['system.performance']['css']['gzip'] = FALSE;
    $config['system.performance']['js']['preprocess'] = FALSE;
    $config['system.performance']['js']['gzip'] = FALSE;
    $config['system.performance']['response']['gzip'] = FALSE;
    $config['views.settings']['ui']['show']['sql_query']['enabled'] = TRUE;
    $config['views.settings']['ui']['show']['performance_statistics'] = TRUE;
    $config['system.logging']['error_level'] = 'all';
    # $settings['cache']['bins']['render'] = 'cache.backend.null';
    # $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

  }

  if (PANTHEON_ENVIRONMENT == 'dev') {
    // Place for settings for the dev environment
  }

  if (PANTHEON_ENVIRONMENT == 'test') {
    // Place for settings for the test environment
    $config['system.performance']['cache']['page']['use_internal'] = TRUE;
    $config['system.performance']['cache']['page']['max_age'] = 900;
    $config['system.performance']['css']['preprocess'] = TRUE;
    $config['system.performance']['css']['gzip'] = TRUE;
    $config['system.performance']['js']['preprocess'] = TRUE;
    $config['system.performance']['js']['gzip'] = TRUE;
    $config['system.performance']['response']['gzip'] = TRUE;
    $config['views.settings']['ui']['show']['sql_query']['enabled'] = FALSE;
    $config['views.settings']['ui']['show']['performance_statistics'] = FALSE;
    $config['system.logging']['error_level'] = 'none';

    // Config Split disable dev modules.
    // $config['config_split.config_split.dev']['status'] = FALSE;
  }

  if (PANTHEON_ENVIRONMENT == 'live') {
    // Place for settings for the live environment
    $conf['reroute_email_enable'] = 0;

    $config['system.performance']['cache']['page']['use_internal'] = TRUE;
    $config['system.performance']['cache']['page']['max_age'] = 900;
    $config['system.performance']['css']['preprocess'] = TRUE;
    $config['system.performance']['css']['gzip'] = TRUE;
    $config['system.performance']['js']['preprocess'] = TRUE;
    $config['system.performance']['js']['gzip'] = TRUE;
    $config['system.performance']['response']['gzip'] = TRUE;
    $config['views.settings']['ui']['show']['sql_query']['enabled'] = FALSE;
    $config['views.settings']['ui']['show']['performance_statistics'] = FALSE;
    $config['system.logging']['error_level'] = 'none';

    // Config Split disable dev modules.
    // $config['config_split.config_split.dev']['status'] = FALSE;

    // Redirect to canonical domain
    if (isset($variables)) {
      if (isset($variables['domains']['canonical'])) {
        if (!$cli) {
          $location = false;
          // Get current protocol
          $protocol = 'http';
          if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
            $protocol = 'https';
          }
          // Default redirect
          $redirect = "$protocol://{$variables['domains']['canonical']}{$_SERVER['REQUEST_URI']}";
          if ($_SERVER['HTTP_HOST'] == $variables['domains']['canonical']) {
            $redirect = false;
          }
          if (isset($variables['domains']['synonyms']) && is_array($variables['domains']['synonyms'])) {
            if (in_array($_SERVER['HTTP_HOST'], $variables['domains']['synonyms'])) {
              $redirect = false;
            }
          }
          if ($redirect) {
            header("HTTP/1.0 301 Moved Permanently");
            header("Location: $redirect");
            exit();
          }
        }
      }
    }
  }

}

/**
 * Solr Settings and local overrides.
 */

//if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
//  // set schema for search_api_solr
//  $conf['pantheon_apachesolr_schema'] = 'sites/all/modules/contrib/search_api_solr/solr-conf/solr-3.x/schema.xml';
//
//  $conf['search_api_attachments_extract_using'] = 'tika';
//  $conf['search_api_attachments_tika_jar'] = 'tika-app-1.1.jar';
//  $conf['search_api_attachments_tika_path'] = '/srv/bin';
//
//  if ($_ENV['PANTHEON_ENVIRONMENT'] == 'lando') {
//    // Override solr server for local dev.
//    $conf['search_api_solr_overrides'] = array(
//      'acquia_search' => array(
//        'name' => 'Pantheon Solr Search (overridden for local lando)',
//        'options' => array(
//          'host' => 'index',
//          'port' => '449',
//          'path' => '/solr',
//        ),
//      ),
//    );
//  }
//}



/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}


