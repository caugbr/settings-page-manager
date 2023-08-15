<?php

class PostPicker {
    private $default_args = [
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish'
    ];
    private $default_cfg = [
        'id' => '',
        'name' => '',
        'title' => 'Post picker',
        'button_label' => 'Select a post',
        'select_label' => 'Select',
        'search_placeholder' => 'Type the post name',
        'multiple' => false,
        'search' => true,
        'value' => []
    ];

    public $posts = [];
    public $args;
    public $cfg;

    public function __construct($cfg = [], $args = []) {
        $this->cfg = array_merge($this->default_cfg, $cfg);
        $this->args = array_merge($this->default_args, $args);
        $this->posts = get_posts($this->args);
    }

    public function picker_js() {
        ?>
        <script>
            function closePicker(evt) {
                if (!evt || !evt.target.matches('.post-picker, .post-picker *')) {
                    const pickers = document.querySelectorAll('.post-picker-input.open');
                    if (pickers.length) {
                        Array.from(pickers).forEach(picker => {
                            console.log('picker', picker)
                            const field = picker.querySelector('#search-field');
                            if (field) {
                                field.value = '';
                                field.dispatchEvent(new Event('input'));
                            }
                            picker.classList.remove('open');
                        });
                    }
                }
            }
            window.addEventListener('load', () => {
                const allPickers = document.querySelectorAll('.post-picker-input');
                if (allPickers.length) {
                    Array.from(allPickers).forEach(picker => {
                        const inputId = picker.getAttribute('data-id');
                        const searchField = picker.querySelector('#search-field');
                        picker.querySelector('.open-picker').addEventListener('click', evt => {
                            const thisPicker = evt.target.closest('.post-picker-input');
                            evt.preventDefault();
                            thisPicker.classList.add('open');
                            const checked = thisPicker.querySelectorAll(`input[name^="${inputId}"]`);
                            Array.from(checked).forEach(inp => {
                                const val = inp.name.replace(/^sett.+\[(\d+)\]$/, '$1');
                                const selector = `.post-picker .stage input[value="${val}"]`;
                                const input = document.querySelector(selector);
                                if (input) {
                                    input.checked = true;
                                }
                            });
                            if (searchField) {
                                searchField.focus();
                            }
                        });
                        picker.querySelector('.close-picker').addEventListener('click', evt => {
                            const thisPicker = evt.target.closest('.post-picker-input');
                            evt.preventDefault();
                            thisPicker.classList.remove('open');
                        });
                        if (searchField) {
                            picker.querySelector('#search-field').addEventListener('input', evt => {
                                const thisPicker = evt.target.closest('.post-picker-input');
                                const labels = thisPicker.querySelectorAll('li > label');
                                if (labels.length) {
                                    const val = evt.target.value.toLowerCase();
                                    Array.from(labels).forEach(label => {
                                        const ival = label.innerHTML.trim().toLowerCase();
                                        const li = label.closest('li');
                                        li.style.display = (!val || ival.includes(val)) ? 'list-item' : 'none';
                                    });
                                }
                            });
                        }
                        picker.querySelector('.picker-select').addEventListener('click', evt => {
                            const thisPicker = evt.target.closest('.post-picker-input');
                            evt.preventDefault();
                            let ret = {};
                            const wrapInputs = thisPicker.querySelector('.picker-inputs');
                            wrapInputs.innerHTML = '';
                            const displayValues = thisPicker.querySelector('.picker-field');
                            displayValues.innerHTML = '';
                            const inputs = thisPicker.querySelectorAll('input:checked');
                            Array.from(inputs).forEach(input => {
                                const id = input.value;
                                const title = input.getAttribute('data-title');
                                ret[id] = title;
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = `${inputId}[${id}]`;
                                hidden.value = title;
                                wrapInputs.appendChild(hidden);
                                displayValues.innerHTML += `#${id} ${title}<br>`;
                            });
                            closePicker();
                        });
                    });
                    document.addEventListener('mousedown', closePicker);
                }
            });
        </script>
        <?php
    }

    public function picker_html() {
        $input_type = $this->cfg['multiple'] ? 'checkbox' : 'radio';
        ?>
        <div class="post-picker-input" data-id="<?php print $this->cfg['name']; ?>">
            <div class="picker-field"><?php
                if (!empty($this->cfg['value'])) {
                    foreach ($this->cfg['value'] as $id => $title) {
                        print "#{$id} {$title}<br>";
                    }
                }
            ?></div>
            <button class="button-secondary open-picker" id="<?php print $this->cfg['id']; ?>-button">
                <?php print $this->cfg['button_label']; ?>
            </button>
            <div class="post-picker <?php print sanitize_title($this->cfg['title']); ?>">
                <form action="#">
                    <header>
                        <a href="#" class="close-picker">&times;</a>
                        <h3><?php print $this->cfg['title']; ?></h3>
                    </header>
                    <?php if ($this->cfg['search']) { ?>
                        <div class="search">
                            <input type="text" id="search-field" placeholder="<?php print $this->cfg['search_placeholder']; ?>">
                        </div>
                    <?php } ?>
                    <div class="stage">
                        <ul>
                            <?php foreach ($this->posts as $post) { ?>
                                <li>
                                    <label for="<?php print $this->cfg['id']; ?>_<?php print $post->ID; ?>">
                                        <input type="<?php print $input_type; ?>" name="posts[]" id="<?php print $this->cfg['id']; ?>_<?php print $post->ID; ?>" value="<?php print $post->ID; ?>" data-title="<?php print $post->post_title; ?>">
                                        <span><?php print $post->post_title; ?></span>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <footer>
                        <button class="picker-select button-secondary">
                            <?php print $this->cfg['select_label']; ?>
                        </button>
                    </footer>
                </form>
            </div>
            <div class="picker-inputs"><?php
                if (!empty($this->cfg['value'])) {
                    foreach ($this->cfg['value'] as $id => $title) {
                        ?>
                        <input type="hidden" name="<?php print $this->cfg['name']; ?>[<?php print $id; ?>]" value="<?php print $title; ?>">
                        <?php
                    }
                }
            ?></div>
        </div>
        <?php
    }
}