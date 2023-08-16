# Manage admin pages for Wordpress themes and plugins
The plugin **Settings Page Manager** is a helper to add admistrative pages to Wordpress. It has no admin interface, just makes available the class ```SettingsPage```, to be directly used by themes or plugins.

## Install
Just go to your plugins admin page and activate Settings Page Manager.

    // check if plugin is active
    if (class_exists('SettingsPage')) {
        // ...
    }

## Class ```SettingsPage```
The constructor expects two parameters. The first one is ```$params```, an associative array containing the configuration for the admin page itsef (see the defaut values below). The second parameter is ```$config_vars```, an array containing the user configuration items.

### ```$params```
See: [add_submenu_page](https://developer.wordpress.org/reference/functions/add_submenu_page/).

    [
        // parameters sent to function add_submenu_page()
        "parent_slug" => "themes.php", // [required] parent menu item (slug)
        "page_title" => "", // [required] used for title tag and <h1> tag
        "menu_title" => "", // [required] label for the menu link
        "capability" => "manage_options", // [required] user capability required
        "menu_slug" => "", // [required] used in page URL and other places
        "position" => NULL, // menu position

        // if is a top level item (parent_slug is empty)
        "icon_url" => "dashicons-admin-generic" // any dashicon
        "link_title" => "", // by default, in a top level menu item, the first link is a duplication
                            // of the top level link. Fill link_title to change the label on first link.

        // other parameters
        "beforeunload_msg" => "" // If not empty, a check will be done before user leaves the page.
                                 // In some browsers this message can be displayed to user.
        "option_name" => "admin_page_settings" // Name for WP option
        "page_intro" => "", // An optional text to display below <h1> tag
        "form_title" => "Options", // An optional subtitle
        "form_intro" => "", // Optional text to display below subtitle
        "button_label" => "Save options", // Label for submit button
        "saved_msg" => "Settings successfully updated", // Message after save options
        "security_msg" => "Security check failed and settings could not be updated", // Identity check failed
    ]

### ```$config_vars```
This is the array that contains your configuration options. For now these are the available input types: ```text``` (or ```email```, ```url```, ```password```, ```tel```, ```number```, ```search```, ```date```, ```datetime-local```), ```post-picker```, ```range```, ```switch```, ```textarea```, ```select```, ```checkbox```, ```checkbox-group``` and ```radio-group```.

    $theme_settings = [
        "hide_wp_bar" => [
            "type" => "checkbox",
            "description" => 'Hide Wordpress admin bar on frontend',
            "label" => 'Hide WP admin bar',
            "default_value" => '0'
        ],
        "expertise" => [
            "type" => "range",
            "description" => 'Define your expertise level',
            "label" => 'Expertise level',
            "default_value" => 0
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
            "description" => 'Textarea field',
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

### Element properties
These are properties shared by all types. There are some specific properties by element type, see below.
 * ```type``` - Element type.\
   One of: ```text``` (or ```email```, ```url```, ```password```, ```tel```, ```number```, ```search```, ```date```, ```datetime-local```), ```post-picker```, ```range```, ```switch```, ```textarea```, ```select```, ```checkbox```, ```checkbox-group``` and ```radio-group```
 * ```label``` - Label, upon the element.
 * ```description``` - Description, above element.
 * ```default_value``` - The default value, if there is no saved value.

**For list elements** (```select```, ```checkbox-group``` and ```radio-group```)
 * ```options``` - An array containing the options.\
 Each option is an array like this: ```[ "label" => "Visible text", "value" => "value property" ]```.

**For types** ```checkbox-group``` **and** ```radio-group```
 * ```inline``` - By default, list items are block elements. Use this option to force list items to be inline elements.

**For text fields** (```text```, ```email```, ```url```, ```password```, ```tel```, ```number```, ```search```, ```date```, ```datetime-local``` and ```textarea```)
 * ```placeholder``` - Text to display when field is empty.

**For type** ```range```
 * ```min``` - Minimum allowed value (default 0)
 * ```max``` - Maximum allowed value (default 100)
 * ```step``` - Step size (default 1)

**For type** ```post-picker```
 * ```post_type``` - Type of posts that will be in posts list
 * ```multiple``` - Allow user to select more than one post
 * ```search``` - Send FALSE to remove the search box
 * ```search_placeholder``` - Text on the search box when it's empty
 
### Example
Check this example for a theme, using some default values:

    // My config items in $config_items
    include_once get_stylesheet_directory() .  "/config-items.php"; 
    $settings = new SettingsPage([
        "page_title" => "Theme admin page",
        "page_intro" => "Welcome text here",
        "menu_title" => "Theme options",
        "menu_slug" => "theme-admin-page",
        "form_title" => "Site options",
        "form_intro" => "Manage your site options here"
    ], $config_items);
    
    // Get the saved options to use in your code
    $theme_options = $settings->get_saved();

## Using tabs to display other contents
You can use the ```tabs``` parameter to add some other HTML to the admin page, as a new tab. In  this case the settings will appear in the first tab that will have the fixed id 'settings' and the label 'Options', but you can specify this using the param ```tab_label```. Each tab is an array with 3 items:
 * ```label``` - the label for the tab link
 * ```callback``` - the name of the rendering function and
 * ```action``` - the value for ```$_POST['action']``` if this tab is visible on form submission.

Remember that you are adding fields to the same form and the submit button will be the same to all tabs, so you should work with ```$_POST['action']``` as a condition to save your content.

Than you can use the filter ```save_admin_page_message``` to save your fields and change the return message.

Example for a plugin:
    
    $settings = new SettingsPage([
        "parent_slug" => "plugins.php",
        "page_title" => "My Plugin admin page",
        "page_intro" => "Welcome text here",
        "menu_title" => "My Plugin options",
        "menu_slug" => "my-plugin-admin-page",
        "form_title" => "",
        "form_intro" => "Manage My Plugin options here"
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

### Using subpages
In the following example, we create a top level item with a subpage. The subpages have their own HTML and processing, but you can also create a separated config file and use the class ```Settings```, used by ```AdminPAge```, to render and save these config items.

    $settings = new SettingsPage([
        "parent_slug" => "",
        "page_title" => "Test admin page",
        "menu_title" => "Admin page",
        "menu_slug" => "theme-admin-page",
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
        include_once get_stylesheet_directory() . "/other-config.php";

        // temp_settings() returns a new instance of Settings,
        // configured to use our wp option 'my_page_settings', based on
        // our configuration variable, $other_settings.
        $ts = $settings->temp_settings('my_page_settings', $other_settings);

        $msg = save_my_page();
        ?>
        <div class="wrap">
            <h1>My plugin</h1>
            <?php $settings->show_message($msg); ?>
            <form action="?page=my-plugin-page" method="post" id="other-page-form" class="settings">
                <?php $ts->render(); // renderize all items in $other_settings ?>
                <div class="formline buttons">
                    <button type="submit" id="save_settings" class="button button-primary">
                        Save
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