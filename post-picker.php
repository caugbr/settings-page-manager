<?php

class PostPicker {
    private $default_args = [
        'posts_per_page' => 15,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish'
    ];
    private $default_cfg = [
        'id' => '',
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
        $this->get_posts();
    }

    public function get_posts() {
        $this->posts = get_posts($this->args);
    }

    public function picker_css() {
        ?>
        <style>
            .post-picker-input {
                display: flex;
                flex-direction: row;
                width: 100%;
                max-width: 500px;
                gap: 0.25rem;
                align-items: flex-start;
            }
            .post-picker-input h3 {
                margin: 0;
            }
            .picker-field {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                background-color: #fff;
                border: 1px solid #8c8f94;
                border-radius: 4px;
                min-height: 1.9rem;
                flex-grow: 5;
                flex-shrink: 5;
            }
            .picker-button {
                width: auto;
                flex-grow: 0;
                flex-shrink: 0;
                height: 1.9rem;
            }
            .close-picker {
                float: right;
                text-decoration: none;
                font-size: 22px;
                font-weight: bold;
                color: #666;
            }
            .post-picker {
                opacity: 0;
                pointer-events: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 560px;
                background-color: #fefefe;
                transition: opacity 250ms ease-in-out 0s;
                border-radius: 6px;
                border: 1px solid #ccc;
                box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
            }
            .post-picker-open .post-picker {
                opacity: 1;
                pointer-events: all;
            }
            .post-picker header {
                padding: 1rem;
            }
            .post-picker .stage {
                padding: 0.5rem 1rem;
                max-height: 15rem;
                overflow: auto;
                background-color: #efefef;                
            }
            .post-picker footer {
                padding: 1rem;
                text-align: right;
            }
        </style>
        <?php
    }

    public function picker_js() {
        ?>
        <script>
            window.addEventListener('load', () => {
                const allPickers = document.querySelectorAll('.post-picker-input');
                if (allPickers.length) {
                    Array.from(allPickers).forEach(picker => {
                        const inputId = picker.getAttribute('data-id');
                        picker.querySelector('.open-picker').addEventListener('click', evt => {
                            evt.preventDefault();
                            document.body.classList.add('post-picker-open');
                            const checked = picker.querySelectorAll(`input[name^="${inputId}"]`);
                            Array.from(checked).forEach(inp => {
                                const val = inp.name.replace(/^sett.+\[(\d+)\]$/, '$1');
                                const selector = `.post-picker .stage input[value="${val}"]`;
                                const input = document.querySelector(selector);
                                if (input) {
                                    input.checked = true;
                                }
                            });
                        });
                        picker.querySelector('.close-picker').addEventListener('click', evt => {
                            evt.preventDefault();
                            document.body.classList.remove('post-picker-open');
                        });
                        picker.querySelector('.picker-select').addEventListener('click', evt => {
                            evt.preventDefault();
                            let ret = {};
                            const wrapInputs = picker.querySelector('.picker-inputs');
                            wrapInputs.innerHTML = '';
                            const displayValues = picker.querySelector('.picker-field');
                            displayValues.innerHTML = '';
                            const inputs = picker.querySelectorAll('input:checked');
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
                            document.body.classList.remove('post-picker-open');
                        });
                    });
                }
            });
        </script>
        <?php
    }

    public function picker_html() {
        $input_type = $this->cfg['multiple'] ? 'checkbox' : 'radio';
        ?>
        <div class="post-picker-input" data-id="<?php print $this->cfg['id']; ?>">
            <div class="picker-field"><?php
                if (!empty($this->cfg['value'])) {
                    foreach ($this->cfg['value'] as $id => $title) {
                        print "#{$id} {$title}<br>";
                    }
                }
            ?></div>
            <button class="button-secondary open-picker"><?php print $this->cfg['button_label']; ?></button>
            <div class="post-picker <?php print sanitize_title($this->cfg['title']); ?>">
                <form action="#">
                    <header>
                        <a href="#" class="close-picker">&times;</a>
                        <h3><?php print $this->cfg['title']; ?></h3>
                    </header>
                    <div class="stage">
                        <ul>
                            <?php foreach ($this->posts as $post) { ?>
                                <li>
                                    <label for="post_<?php print $post->ID; ?>">
                                        <input type="<?php print $input_type; ?>" name="posts[]" id="post_<?php print $post->ID; ?>" value="<?php print $post->ID; ?>" data-title="<?php print $post->post_title; ?>">
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
                        <input type="hidden" name="<?php print $this->cfg['id']; ?>[<?php print $id; ?>]" value="<?php print $title; ?>">
                        <?php
                    }
                }
            ?></div>
        </div>
        <?php
    }
}