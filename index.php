<?php
/*
 * Plugin Name: WP Admin Page
 * Description: Helper on adding administrative pages to WP admin.
 * Version: 1.0.0
 * Author: Cau Guanabara
 * Author URI: https://github.com/caugbr
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

require dirname(__FILE__) . "/components/settings.php";

class AdminPage {

    public $parent_slug = 'themes.php';
    public $page_title = '';
    public $menu_title = '';
    public $capability = 'manage_options';
    public $menu_slug = '';
    public $position = NULL;
    public $icon_url = 'dashicons-admin-generic';
    public $link_title = '';

    public $page_intro = '';
    public $form_title = 'Options';
    public $tab_label = 'Options';
    public $form_intro = '';
    public $button_label = 'Save options';
    public $saved_msg = 'Settings successfully updated';
    public $security_msg = 'Security check failed and settings were not updated';

    public $tabs = [];
    public $subpages = [];
    public $beforeunload_msg = '';
    public $option_name = 'admin_page_settings';
    
    // Internal
    private $base_url;
    private $page_url;
    private $page_id;
    private $subpage_ids = [];
    private $scripts = [];
    private $styles = [];
    private $settings;

    public function __construct($params = [], $config_var = []) {
        foreach ($params as $pname => $param) {
            if (property_exists($this, $pname)) {
                $this->{$pname} = $param;
            }
            if ($pname == '__scripts') {
                foreach ($param as $id => $url) {
                    $this->scripts[$id] = $url;
                }
            }
            if ($pname == '__styles') {
                foreach ($param as $id => $url) {
                    $this->styles[$id] = $url;
                }
            }
        }
        $this->base_url = plugin_dir_url(__FILE__);
        $this->page_url = admin_url() . $this->admin_path();
        $this->settings = new ThemeSettings($this->option_name, $config_var);

        // user cannot use 'settings' as tab id, it's reserved.
        @unlink($this->tabs['settings']);

        add_action('admin_menu', [$this, 'add_page']);
    }

    public function temp_settings($option_name, $config_var = []) {
        return new ThemeSettings($option_name, $config_var);
    }
    
    public function admin_path() {
        $slug = empty($this->parent_slug) ? 'admin.php' : $this->parent_slug;
        $sign = strstr($slug, '?') ? '&' : '?';
        return $slug . $sign . "page=" . $this->menu_slug;
    }
    
    public function render() {
        return $this->settings->render();
    }
    
    public function get_saved() {
        return $this->settings->get_saved();
    }
    
    public function add_page() {
        if ($this->parent_slug) {
            $this->page_id = add_submenu_page(
                $this->parent_slug,
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->menu_slug,
                [$this, 'admin_page'],
                $this->position
            );
        } else {
            $this->page_id = add_menu_page(
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->menu_slug,
                [$this, 'admin_page'],
                $this->icon_url,
                $this->position
            );
            if (!empty($this->subpages)) {
                if (!empty($this->link_title)) {
                    add_submenu_page(
                        $this->menu_slug,
                        $this->page_title,
                        $this->link_title,
                        $this->capability,
                        $this->menu_slug,
                        [$this, 'admin_page'],
                        $this->position
                    );
                }
                foreach ($this->subpages as $page) {
                    $this->subpage_ids[$page['menu_slug']] = add_submenu_page(
                        $this->menu_slug,
                        $page['page_title'],
                        $page['menu_title'],
                        $this->capability,
                        $page['menu_slug'],
                        $page['callback']
                    );
                }
            }
        }
        $this->add_stuff();
        if (!empty($this->subpage_ids)) {
            foreach ($this->subpage_ids as $pid) {
                $this->add_stuff($pid);
            }
        }
    }

    public function add_stuff($page_id = false) {
        $page_id = $page_id ? $page_id : $this->page_id;
        add_action('admin_print_scripts-' . $page_id, [$this, 'add_js']);
        add_action('admin_print_styles-' . $page_id, [$this, 'add_css'] );
    }
    
    public function add_js() {
        wp_enqueue_script("admp-admin-js", $this->base_url . "assets/admin.js");
        wp_localize_script('admp-admin-js', 'messages', [ "beforeunload_msg" => $this->beforeunload_msg ]);
        foreach ($this->scripts as $id => $url) {
            wp_enqueue_script($id, $url);
        }
    }
    
    public function add_css() {
        wp_enqueue_style("admp-admin-css", $this->base_url . "assets/admin.css");
        foreach ($this->styles as $id => $url) {
            wp_enqueue_style($id, $url);
        }
    }

    private function save() {
        $msg = '';
        if (!empty($_POST['action'])) {
            if(!wp_verify_nonce($_POST['security'], 'admin_page')) {
                return $this->security_msg;
            }
            if (!empty($_POST['settings']) && $_POST['action'] == 'save-settings') {
                $this->settings->save($_POST['settings']);
                $msg = $this->saved_msg;
            }
            $msg = apply_filters('save_admin_page_message', $msg);
        }
        return $msg;
    }
    
    public function admin_page() {
        $msg = $this->save();
        ?>
        <div class="wrap">
            <h1><?php print $this->page_title; ?></h1>

            <?php if ($this->page_intro) { ?>
                <p><?php print $this->page_intro; ?></p>
            <?php } ?>

            <?php $this->show_message($msg); ?>

            <div class="settings">
                <form action="<?php print $this->page_url; ?>" method="post" id="admin-page-form">
                <div class="tabs" data-tab="settings">

                    <?php if (!empty($this->tabs)) { ?>
                    <div class="tab-links">
                        <a class="tab" href="#" data-tab="settings" data-action="save-settings">
                            <?php print $this->tab_label; ?>
                        </a>
                        <?php foreach ($this->tabs as $id => $info) { ?>
                        <a class="tab" href="#" data-tab="<?php print $id; ?>" data-action="<?php print $info['action'] ?? 'save-settings'; ?>">
                            <?php print $info['label']; ?>
                        </a>
                        <?php } ?>
                    </div>
                    <div class="tab-stage">
                        <div class="tab-content" data-tab="settings">
                    <?php } ?>

                    <?php if ($this->form_title) { ?>
                        <h2><?php print $this->form_title; ?></h2>
                    <?php } ?>

                    <?php if ($this->form_intro) { ?>
                        <p><?php print $this->form_intro; ?></p>
                    <?php } ?>

                    <?php $this->render(); ?>

                    <?php if (!empty($this->tabs)) { ?>
                        </div>
                        <?php foreach ($this->tabs as $id => $info) { ?>
                            <div class="tab-content" data-tab="<?php print $id; ?>">
                            <?php print call_user_func($info['callback']); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
        
                    <div class="formline buttons">
                        <button type="submit" id="save_settings" class="button button-primary">
                            <?php print $this->button_label; ?>
                        </button>
                    </div>
                    <input type="hidden" name="action" value="save-settings">
                    <?php wp_nonce_field('admin_page', 'security'); ?>
                </form>
            </div>
        </div>
        <?php
        $this->tabs_css();
    }

    public function show_message($msg, $is_error = false, $is_dismissible = true) {
        if (!empty($msg)) {
            $cls = $is_error ? 'notice-error' : 'notice-success';
            if ($is_dismissible) {
                $cls .= ' is-dismissible';
            }
            ?>
            <div id="message" class="notice <?php print $cls; ?>">
                <p><strong><?php print $msg; ?></strong></p>
                <?php if ($is_dismissible) { ?>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                <?php } ?>
            </div>
            <?php
        }
    }

    private function tabs_css() {
        if (!empty($this->tabs)) {
        ?>
        <style>
            <?php foreach (array_keys($this->tabs) as $id) { ?>
            .settings .tabs[data-tab="<?php print $id; ?>"] .tab-links a.tab[data-tab="<?php print $id; ?>"],
            <?php } ?>
            .settings .tabs[data-tab="settings"] .tab-links a.tab[data-tab="settings"] {
                background-color: #efefef;
                border-bottom-color: #efefef;
                font-weight: bold;
                cursor: default;
            }
            <?php foreach (array_keys($this->tabs) as $id) { ?>
            .settings .tabs[data-tab="<?php print $id; ?>"] .tab-stage .tab-content[data-tab="<?php print $id; ?>"],
            <?php } ?>
            .settings .tabs[data-tab="settings"] .tab-stage .tab-content[data-tab="settings"] {
                display: block;
            }
        </style>
        <?php
        }
    }
}