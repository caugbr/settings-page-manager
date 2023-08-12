# Admin Page for Wordpress themes and plugins
This is a package to add an admistrative page in Wordpress, that can be used by themes and plugins.

## Install
There's no installation needed, just add the entire folder ```wp-admin-page``` to the main folder of your theme or plugin.

*Using Git*\
Open a terminal, navigate to the main folder of your theme ou plugin, type ```git clone https://github.com/caugbr/wp-admin-page.git wp-admin-page``` and press Enter.\
*Download*\
Create the folder ```wp-admin-page``` on the main folder of your theme or plugin and paste all content inside it.

The ```admin-page/settings-config.php``` file contains the ```$theme_settings``` variable, which contains all the configuration items you've chosen to work in your theme or plugin. Just set this variable, include the file ```admin-page/index.php``` and instantiate the class ```AdminPage```.

### Fill ```$theme_settings```
This is the array that contains your configuration options. For now these are the available input types: ```text``` (or ```email```, ```url```, ```password```, ```tel```, ```number```, ```search```, ```date```, ```datetime-local```), ```switch```, ```textarea```, ```select```, ```checkbox```, ```checkbox-group``` and ```radio-group```.

    $theme_settings = [
        "hide_wp_bar" => [
            "type" => "checkbox",
            "description" => 'Hide Wordpress admin bar on frontend',
            "label" => 'Hide WP admin bar',
            "default_value" => '0'
        ],
        "sidebar_location" => [
            "type" => "select",
            "options" => [
                [ "label" => "On the left", "value" => "left" ],
                [ "label" => "On the right", "value" => "right" ],
                [ "label" => "Don't use sidebar", "value" => "none" ]
            ],
            "description" => 'Define where to show sidebar',
            "label" => 'Sidebar position',
            "default_value" => 'left'
        ],
        "post_formats" => [
            "type" => "checkbox-group",
            "options" => "all_post_formats", // you can use a function name here
            "description" => 'Select the post formats that will have support in your theme.',
            "label" =>  'Post formats support',
            "default_value" => ''
        ],
        "text_field" => [
            "type" => "text",
            "description" => 'Text field',
            "label" =>  'Type something',
            "placeholder" => "Type here",
            "default_value" => ''
        ],
        "area_field" => [
            "type" => "textarea",
            "description" => 'Test area field',
            "label" =>  'Type a long text',
            "default_value" => 'blah'
        ],
        "radio_list" => [
            "type" => "radio-group",
            "description" => 'Choose one',
            "label" =>  'Radio group',
            "options" => [
                [ "value" => "yes", "label" => "Yes" ],
                [ "value" => "no", "label" => "No" ],
                [ "value" => "maybe", "label" => "Maybe" ]
            ],
            "default_value" => 'yes'
        ]
    ]

    function all_post_formats() {
        return [
            [ "label" => __('Aside'), "value" => 'aside' ],
            [ "label" => __('Gallery'), "value" => 'gallery' ],
            [ "label" => __('Link'), "value" => 'link' ],
            [ "label" => __('Image'), "value" => 'image' ],
            [ "label" => __('Quote'),  "value" => 'quote' ],
            [ "label" => __('Status'), "value" => 'status' ],
            [ "label" => __('Video'), "value" => 'video' ],
            [ "label" => __('Audio'), "value" => 'audio' ],
            [ "label" => __('Chat'), "value" => 'chat' ]
        ];
    }

## Class ```AdminPage```
The constructor expects one parameter, an associative array containing all variables. These are the defaut values:\
See: [add_submenu_page](https://developer.wordpress.org/reference/functions/add_submenu_page/).

    [
        // parameters sent to function add_submenu_page()
        "parent_slug" => "themes.php", // [required]
        "page_title" => "", // [required] used also in <h1> tag
        "menu_title" => "", // [required]
        "capability" => "manage_options", // [required]
        "menu_slug" => "", // [required]
        "position" => NULL,

        // if is a top level item (parent_slug is empty)
        "icon_url" => "dashicons-admin-generic" // any dashicon
        "link_title" => "", // by default, in a top level menu item, the first link is a duplication
                            // of the top level link. Fill link_title to change the label on first link.

        // other parameters
        "page_intro" => "", // An optional text to display below <h1> tag
        "form_title" => "Options", // An optional subtitle
        "form_intro" => "", // Optional text to display below subtitle
        "button_label" => "Save options", // Label for submit button
        "saved_msg" => "Settings successfully updated", // Message after save options
        "security_msg" => "Security check failed and settings could not be updated", // Identity check failed
        "base_url" => "" // [required] The URL to theme or plugin folder
    ]
 
### Example
Check this example for a theme, using some default values:

    // Settings
    include_once get_stylesheet_directory() .  "/wp-admin-page/index.php";
    $settings = new AdminPage([
        "page_title" => "Theme admin page",
        "page_intro" => "Welcome text here",
        "menu_title" => "Theme options",
        "menu_slug" => "theme-admin-page",
        "form_title" => "Site options",
        "form_intro" => "Manage your site options here",
        "base_url" => get_stylesheet_directory_uri()
    ]);
    
    // Get the saved options to use in your code
    $theme_options = $settings->get_saved();

## Using tabs to display other contents
You can use the ```tabs``` parameter to add some other HTML to the admin page. In  this case the settings will appear in the first tab that will have the fixed id 'settings' and the label 'Options', but you can specify it using the param ```tab_label```. Each tab is an array with 3 items, ```label``` - the label for the tab link, ```callback``` - the name of the rendering function and ```action``` - the value for ```$_POST['action']``` if this tab is visible on form submission. Remember that you are adding fields to the same form and the submit button will be the same to all tabs, so you should work with $_POST['action'] as a condition to save your content.

Than you can use the filter ```save_admin_page_message``` to save your fields an change the return message.

Example for a plugin:
    
    include_once plugin_dir_path(__FILE__) .  "wp-admin-page/index.php";
    $settings = new AdminPage([
        "parent_slug" => "plugins.php",
        "page_title" => "My Plugin admin page",
        "page_intro" => "Welcome text here",
        "menu_title" => "My Plugin options",
        "menu_slug" => "my-plugin-admin-page",
        "form_title" => "",
        "form_intro" => "Manage My Plugin options here",
        "base_url" => plugins_url() . "/my-plugin",
        "tabs" => [
            "label" => "My fields",
            "callback" => "my_fields_html",
            "action" => "save-my-field"
        ]
    ]);

    function my_fields_html() {
        ?>
        <h2>My plugin options</h2>
        <div class="formline">
            <label for="my_field">My field</label>
            <input type="text" name="my_field">
        </div>
        <?php
    }

    function save_my_fields($msg) {
        if ($_POST['action'] == 'save-my-field') {
            // save field here...
            $msg = "The value of My field ({$_POST['my_field']}) was updated.";
        }
        return $msg;
    }
    add_filter('save_admin_page_message', 'save_my_fields');


## Link as a top level menu item
You can create the menu link as a top level item and, optionally, work with subpages. To configure your page to be a top level menu item, just send ```parent_slug``` as an empty string.

## Using subpages
In the below example, we create a top level item with one subpage. The subpages have their own HTML and processing, but you can also create a new config file and use the class ```ThemeSettings```, used by ```AdminPAge```, to render and save these config items.

    include_once get_stylesheet_directory() . "/wp-admin-page/index.php";
    $settings = new AdminPage([
        "parent_slug" => "",
        "page_title" => "Test admin page",
        "menu_title" => "Admin page",
        "menu_slug" => "theme-admin-page",
        "base_url" => get_stylesheet_directory_uri(),
        "subpages" => [
            [
                "page_title" => "My plugin options",
                "menu_title" => "My plugin",
                "menu_slug" => "my-plugin-page",
                "callback" => "my_page_html"
            ]
        ],
        "link_title" => "Site options"
    ]);
    
    function my_page_html() {
        global $settings;

        // include $other_settings
        include_once get_stylesheet_directory() . "/wp-admin-page/other-config.php";

        // temp_settings() returns a new instance of ThemeSettings,
        // configured to use our wp option 'my_page_settings'
        $ts = $settings->temp_settings('my_page_settings');

        // when class loads, it automatically reads the default file / variable
        // (settings-config.php / $theme_settings), so we have to 
        // send our variable to replace it, using set_settings()
        $ts->set_settings($other_settings);

        $msg = save_my_page();
        ?>
        <div class="wrap">
            <h1>My plugin</h1>
            <?php if (!empty($msg)) { ?>
                <div id="message" class="notice notice-success settings-error is-dismissible">
                    <p><strong><?php print $msg; ?></strong></p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            <?php } ?>
            <form action="?page=my-plugin-page" method="post" id="other-page-form" class="settings">
                <?php $ts->render(); // renderize all items in $other_settings ?>
                <div class="formline buttons">
                    <button type="submit" id="save_settings" class="button button-primary">
                        Salvar
                    </button>
                </div>
                <input type="hidden" name="action" value="save-other">
                <?php wp_nonce_field('admin_page', 'security'); ?>
            </form>
        </div>
        <?php
    }

    function save_my_page() {
        $msg = '';
        if (isset($_POST['action']) && $_POST['action'] == 'save-other') {
            global $settings;
            if(!wp_verify_nonce($_POST['security'], 'admin_page')) {
                return $settings->security_msg;
            }
            $ts = $settings->temp_settings('my_page_settings');
            $ts->save($_POST['settings']);
            $msg = "My page was updated.";
        }
        return $msg;
    }