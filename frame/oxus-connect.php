<?php

class Oxus_Connect
{

    private $oxus_stop = false;


    public function __construct()
    {



        if (!class_exists('OXUS_Plugin_Updater')) {
            // load our custom updater
            include OXUS_FRAME . '/admin/updater/OXUS_Plugin_Updater.php';
        }

        // To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
        $doing_cron = defined('DOING_CRON') && DOING_CRON;
        if (!current_user_can('manage_options') && !$doing_cron) {
            return;
        }

        // retrieve our license key from the DB
        $license_key = trim(get_option('oxus_license_key'));

        // setup the updater
        $edd_updater = new OXUS_Plugin_Updater(
            OXUS_STORE_URL,
            OXUS_MAIN,
            array(
                'version' => OXUS_VERSION,
                'license' => $license_key,
                'item_id' => OXUS_ITEM_ID,
                'author' => 'Luxibay',
                'beta' => false,
            )
        );

        $this->oxus_admin_callback();

        $this->oxus_dependency_check();

        if ($this->oxus_stop == false) {

            /* Integrate the plugins */
            add_action('oxus_integrate', array($this, 'oxus_integrate_callback'), 10, 2);
            do_action('oxus_integrate');

            /* Apply any patches */
            add_action('oxus_mend', array($this, 'oxus_mend_callback'), 10, 2);
            do_action('oxus_mend');
        }
    }

    function oxus_dependency_check()
    {

        require_once(OXUS_PATH . '/vendor/autoload.php');


        if (!isset($oxus_notices)) {
            $oxus_notices = new \WPTRT\AdminNotices\Notices();
        }

        /* Abort launch if WPML doesn't exist */
        if (!class_exists('SitePress')) {
            $this->oxus_stop = true;
            $oxus_notices->add(
                'hookline_wpml',
                '',
                __(OXUS_ITEM_NAME . ' requires the WPML plugin to be installed and activated to work', 'oxus'),
                [
                    'type' => 'warning',
                ]
            );
        }

        /* Abort launch if Oxygen doesn't exist */
        if (!class_exists('OxygenElement')) {
            $this->oxus_stop = true;
            $oxus_notices->add(
                'hookline_oxygen',
                '',
                __(OXUS_ITEM_NAME . ' requires the Oxygen plugin to be installed and activated to work', 'oxus'),
                [
                    'type' => 'warning',
                ]
            );
        }

        $oxus_notices->boot();
    }

    function oxus_admin_callback()
    {
        /* Load Admin features */
        foreach (glob(OXUS_FRAME . "/admin/*.php") as $file) {
            require $file;
        }

        /* Include Notices */
        foreach (glob(OXUS_FRAME . "/admin/wptrt/admin-notices/src/*.php") as $filename) {
            include $filename;
        }
    }

    function oxus_integrate_callback()
    {



        /* Integrate the plugins */
        foreach (glob(OXUS_FRAME . "/includes/*.php") as $file) {
            require $file;
        }
    }

    function oxus_mend_callback()
    {
        /* Apply any patches */
        foreach (glob(OXUS_FRAME . '/patches/*.php') as $file) {
            require $file;
        }
    }
}
