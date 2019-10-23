<?php

/*
  PHP file containing uninstall procedure.
 */

// delete plugin options:

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('DpdBukvicjaAdminOptions');
