<?php
include_once dirname(__FILE__) . "/settings-config.php";

class ThemeSettings {

    var $settings;
    var $option_name;

    function __construct($opt_name) {
        global $theme_settings;

        $this->option_name = $opt_name;
        $this->settings = $theme_settings;
    }

    public function set_settings($settings) {
        $this->settings = $settings;
    }

    public function options($opts) {
        if (!is_array($opts) && is_callable($opts)) {
            $opts = call_user_func($opts);
        }
        return is_array($opts) ? $opts : [];
    }

    public function render() {
        $textRe = "/^(text|email|url|password|tel|number|search|date|datetime-local)$/";
        $saved = $this->get_saved();
        
        foreach ($this->settings as $id => $info) {
            $info['default_value'] = $info['default_value'] ?? '';
            $info['options'] = $info['options'] ?? [];
            $value = $saved[$id] ?? $info['default_value'];
            $options = $this->options($info['options']);
            ?>
            <div class="formline">
                <?php if ($info['type'] == 'checkbox') { ?>
                    <label for="<?php print $id; ?>">
                        <input type="hidden" name="settings[<?php print $id; ?>]" value="0">
                        <input 
                            type="checkbox" 
                            name="settings[<?php print $id; ?>]" 
                            id="<?php print $id; ?>" 
                            value="1"
                            <?php if ($value == '1') print ' checked'; ?>
                        >
                        <?php print $info['label']; ?>
                    </label>
                <?php } ?>
    
                <?php if ($info['type'] == 'switch') { ?>
                    <label for="<?php print $id; ?>">
                        <input type="hidden" name="settings[<?php print $id; ?>]" value="0">
                        <span class="switch">
                            <input 
                                type="checkbox" 
                                name="settings[<?php print $id; ?>]" 
                                id="<?php print $id; ?>" 
                                value="1"
                                <?php if ($value == '1') print ' checked'; ?>
                            >
                            <span class="slider"></span>
                        </span>
                        <?php print $info['label']; ?>
                    </label>
                <?php } ?>
    
                <?php if ($info['type'] == 'select') { ?>
                    <label for="<?php print $id; ?>"><?php print $info['label']; ?></label>
                    <select name="settings[<?php print $id; ?>]" id="<?php print $id; ?>">
                        <?php foreach($options as $opt) { ?>
                            <option 
                                value="<?php print $opt['value'] ?>"
                                <?php if ($opt['value'] == $value) print ' selected'; ?>
                            ><?php print $opt['label'] ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
    
                <?php if ($info['type'] == 'textarea') { ?>
                    <label for="<?php print $id; ?>"><?php print $info['label']; ?></label>
                    <textarea 
                        name="settings[<?php print $id; ?>]" 
                        id="<?php print $id; ?>"
                        <?php if (!empty($info['placeholder'])) {
                            print "placeholder=\"{$info['placeholder']}\"";
                        } ?>
                    ><?php print $saved[$id] ?? $info['default_value']; ?></textarea>
                <?php } ?>
    
                <?php if (preg_match($textRe, $info['type'])) { ?>
                    <label for="<?php print $id; ?>"><?php print $info['label']; ?></label>
                    <input 
                        type="<?php print $info['type']; ?>" 
                        value="<?php print $saved[$id] ?? $info['default_value']; ?>" 
                        name="settings[<?php print $id; ?>]" 
                        id="<?php print $id; ?>"
                        <?php if (!empty($info['placeholder'])) {
                            print " placeholder=\"{$info['placeholder']}\"";
                        } ?>
                    >
                <?php } ?>
    
                <?php if ('radio-group' == $info['type']) { ?>
                    <label for="<?php print "{$id}-0"; ?>"><?php print $info['label']; ?></label>
                    <?php foreach($options as $i => $opt) { ?>
                        <label for="<?php print "{$id}-{$i}"; ?>">
                            <input 
                                type="radio" 
                                name="settings[<?php print $id; ?>]" 
                                id="<?php print "{$id}-{$i}"; ?>" 
                                value="<?php print $opt['value']; ?>"
                                <?php if ($value == $opt['value']) print ' checked'; ?>
                            >
                            <?php print $opt['label']; ?>
                        </label>
                    <?php } ?>
                <?php } ?>
    
                <?php if ('checkbox-group' == $info['type']) { ?>
                    <label for="<?php print "{$id}-0"; ?>"><?php print $info['label']; ?></label>
                    <?php foreach($options as $i => $opt) { ?>
                        <label for="<?php print "{$id}-{$i}"; ?>">
                            <input 
                                type="checkbox" 
                                name="settings[<?php print $id; ?>][]" 
                                id="<?php print "{$id}-{$i}"; ?>" 
                                value="<?php print $opt['value']; ?>"
                                <?php if (in_array($opt['value'], (array)$value)) {
                                    print ' checked';
                                } ?>
                            >
                            <?php print $opt['label']; ?>
                        </label>
                    <?php } ?>
                <?php } ?>
    
                <div class="info">
                    <?php print $info['description']; ?>
                </div>
            </div>
            <?php 
        }
    }

    public function hidden($name, $value) {
        ?>
        <input type="hidden" name="<?php print $name; ?>" value="<?php print $value; ?>">
        <?php
    }

    public function get_saved() {
        $saved = get_option($this->option_name);
        if (!$saved) {
            $saved = [];
            foreach ($this->settings as $id => $info) {
                $saved[$id] = $info['default_value'] ?? '';
            }
        }
        return $saved;
    }

    public function save($settings) {
        $ret = update_option($this->option_name, $settings);
        return $ret;
    }
}