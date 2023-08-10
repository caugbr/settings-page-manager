<?php
require dirname(__FILE__) . "/settings.php";

class AdminPage {

    var $parent_slug = 'themes.php';
    var $page_title = '';
    var $menu_title = '';
    var $capability = 'manage_options';
    var $menu_slug = '';
    var $position = NULL;

    var $page_intro = '';
    var $form_title = 'Options';
    var $form_intro = '';
    var $button_label = 'Save options';
    var $saved_msg = 'Settings successfully updated';
    var $security_msg = 'Security check failed and settings were not updated';

    var $base_url;
    var $page_url;
    var $page_id;

    var $settings;
    var $saved_options = [];

    public function __construct($params) {
        foreach ($params as $pname => $param) {
            if (property_exists($this, $pname)) {
                $this->{$pname} = $param;
            }
        }
        $this->page_url = admin_url() . $this->admin_path();
        $this->settings = new ThemeSettings('admin_page_settings');
        $this->saved_options = $this->settings->get_saved();
        add_action('admin_menu', [$this, 'add_page']);
    }
    
    public function admin_path() {
        $sign = strstr($this->parent_slug, '?') ? '&' : '?';
        return $this->parent_slug . $sign . "page=" . $this->menu_slug;
    }
    
    public function add_page() {
        $this->page_id = add_submenu_page(
            $this->parent_slug,
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            [$this, 'admin_page'],
            $this->position
        );
        add_action('admin_print_scripts-' . $this->page_id, [$this, 'add_js']);
        add_action('admin_print_styles-' . $this->page_id, [$this, 'add_css'] );
    }
    
    public function add_js() {
        wp_enqueue_script("admp-admin-js", $this->base_url . "/admin-page/assets/admin.js");
    }
    
    public function add_css() {
        wp_enqueue_style("admp-admin-css", $this->base_url . "/admin-page/assets/admin.css");
    }

    private function save() {
        $msg = '';
        if (!empty($_POST['action']) && $_POST['action'] == 'save-settings') {
            if(!wp_verify_nonce($_REQUEST['security'], 'admin_page')) {
                $msg = $this->security_msg;
            } else {
                $this->settings->save($_POST['settings']);
                $msg = $this->saved_msg;
            }
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
                    <?php if ($this->form_title) { ?>
                        <h2><?php print $this->form_title; ?></h2>
                    <?php } ?>

                    <?php if ($this->form_intro) { ?>
                        <p><?php print $this->form_intro; ?></p>
                    <?php } ?>

                    <?php $this->settings->render(); ?>

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
    }
}