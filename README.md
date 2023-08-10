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
	include_once  get_stylesheet_directory() .  "/admin-page/index.php";
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
