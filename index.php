<?php
require dirname(__FILE__) . "/settings.php";

class AdminPage {

    var $parent_slug = 'themes.php';
    var $page_title = '';
    var $menu_title = '';
    var $capability = 'manage_options';
    var $menu_slug = '';
    var $position = NULL;
    var $icon_url = 'dashicons-admin-generic';
    var $link_title = '';

    var $page_intro = '';
    var $form_title = 'Options';
    var $tab_label = 'Options';
    var $form_intro = '';
    var $button_label = 'Save options';
    var $saved_msg = 'Settings successfully updated';
    var $security_msg = 'Security check failed and settings were not updated';

    var $base_url;
    var $page_url;
    var $page_id;
    var $tabs = [];
    var $subpages = [];
    var $subpage_ids = [];

    var $settings;
    var $tmp;

    public function __construct($params = []) {
        foreach ($params as $pname => $param) {
            if (property_exists($this, $pname)) {
                $this->{$pname} = $param;
            }
        }
        $this->page_url = admin_url() . $this->admin_path();
        $this->settings = new ThemeSettings('admin_page_settings');

        // user cannot use 'settings' as tab id, it's reserved.
        @unlink($this->tabs['settings']);

        add_action('admin_menu', [$this, 'add_page']);
    }

    public function temp_settings($option_name) {
        return new ThemeSettings($option_name);
    }
    
    public function admin_path() {
        $slug = empty($this->parent_slug) ? 'admin.php' : $this->parent_slug;
        $sign = strstr($slug, '?') ? '&' : '?';
        return $slug . $sign . "page=" . $this->menu_slug;
    }
    
    public function render() {
        return $this->settings->render();
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
        wp_enqueue_script("admp-admin-js", $this->base_url . "/wp-admin-page/assets/admin.js");
    }
    
    public function add_css() {
        wp_enqueue_style("admp-admin-css", $this->base_url . "/wp-admin-page/assets/admin.css");
    }

    private function save() {
        $msg = '';
        if (!empty($_POST['action']) && !empty($_POST['settings'])) {
            if(!wp_verify_nonce($_POST['security'], 'admin_page')) {
                return $this->security_msg;
            }
            if ($_POST['action'] == 'save-settings') {
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

            <?php if (!empty($msg)) { ?>
                <div id="message" class="notice notice-success settings-error is-dismissible">
                    <p>
                        <strong><?php print $msg; ?></strong>
                    </p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            <?php } ?>

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

    private function tabs_css() {
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