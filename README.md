# Admin Page for themes and plugins
This is a package to add an admistrative page in Wordpress, that can be used in themes and plugins.

The ```admin-page/settings-config.php``` file contains the ```$theme_settings``` variable, which contains all the configuration items you've chosen to work in your theme or plugin. Just set this variable, include the file ```admin-page/index.php``` and instantiate the class ```AdminPage```.

## Class ```AdminPage```
The constructor expects one parameter, an associative array containing all variables. These are the defaut values:
See: [add_submenu_page](https://developer.wordpress.org/reference/functions/add_submenu_page/).

    [
        // parameters sent to function add_submenu_page()
        "parent_slug" => "themes.php",
        "page_title" => "", // used also in <h1> tag
        "menu_title" => "",
        "capability" => "manage_options",
        "menu_slug" => "",
        "position" => NULL,

        // other parameters
        "page_intro" => "", // An optional text to display below <h1> tag
        "form_title" => "Options", // An optional subtitle
        "form_intro" => "", // Optional text to display below subtitle
        "button_label" => "Save options", // Label for submit button
        "saved_msg" => "Settings successfully updated", // Message after save options
        "security_msg" => "Security check failed and settings were not updated", // Identity check failed
        "base_url" => "" // The URL to theme or plugin folder
    ]
 
### Example
Check this example for a theme, using some default values:

    // Settings
    include_once  get_stylesheet_directory() .  "/wp-admin-page/index.php";
    $settings = new  AdminPage([
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


 ### Fill ```$theme_settings```
This is the array that contains your configuration options. For now these are the available input types: ```text```, ```email```, ```url```, ```password```, ```tel```, ```number```, ```search```, ```date```, ```datetime-local```, ```textarea```, ```select```, ```checkbox```, ```checkbox-group``` and ```radio-group```.

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

## Using tabs to display other contents
You can use the ```tabs``` parameter to add some other HTML to the admin page. In  this case the settings will appear in the first tab that will have the fixed id 'settings' and the label 'Options', but you can specify it using the param ```tab_label```. Each tab is an array with two items, 'label' - the label for the tab link and 'callback' - the name of the rendering function. Remember that you are adding fields to the same form and the submit button will be the same to all tabs.

Than you can use the filter ```save_admin_page_message``` to save your fields an change the return message.

Example:
    
    // Settings
    include_once  plugins_url() .  "/my-plugin-dir/wp-admin-page/index.php";
    $settings = new  AdminPage([
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