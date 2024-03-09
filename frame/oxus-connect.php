<?php

class Oxus_Connect
{

    private $oxus_stop = false;


    public function __construct()
    {

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
