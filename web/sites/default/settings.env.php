<?php

// First, try to determine where we are running.
$infra = match(true) {
  getenv('IS_DDEV_PROJECT') == 'true' => 'ddev',
  !empty($_ENV['AH_SITE_ENVIRONMENT']) => 'acquia',
  !empty($_ENV['PANTHEON_ENVIRONMENT']) => 'pantheon',
  !empty($_ENV['PLATFORM_VARIABLES']) => 'platform',
};

// Based on where we are running, find out the environment.
$env_type = '';
$env = '';
$release = '';
switch ($infra) {
  case 'ddev':
    $env_type = 'local';
    $env = 'local';
    // Leave release as blank for now.
    // Possible candidates for the future:
    // Git commit ID, branch name, etc.
    $release = '';
    break;

  case 'acquia':
    $env_type = getenv('AH_SITE_ENVIRONMENT');
    $env = $env_type;
    // TODO This needs investigation to see if it is possible.
    $release = '';
    break;

  case 'pantheon':
    $env_type = getenv('PANTHEON_ENVIRONMENT');
    $env = $env_type;
    // TODO This needs investigation to see if it is possible.
    $release = '';
    break;

  case 'platform':
    $env_type = getenv('PLATFORM_ENVIRONMENT_TYPE');
    $branch = getenv('PLATFORM_BRANCH');
    $env = $branch == 'main'
      ? 'production'
      : preg_replace('/[^A-Za-z0-9]/', '-', $branch);

    // While this can help us classify builds, it is difficult to use
    // to figure out which commit is deployed. But this is the best we
    // have right now.
    $release = getenv('PLATFORM_TREE_ID');
    break;
}

// Use these variables to set whatever needs setting.
$_SERVER['SENTRY_ENVIRONMENT'] = $env;
$_SERVER['SENTRY_RELEASE'] = $release;
$config['environment_indicator.indicator']['name'] = $env;
