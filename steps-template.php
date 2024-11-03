<?php
$steps = get_post_meta($post->ID, 'wodx_form_steps', true);
if (!is_array($steps)) {
    $steps = array();
}

// Get the general fields
$final_title = get_post_meta($post->ID, 'wodx_form_final_title', true);
$final_text = get_post_meta($post->ID, 'wodx_form_final_text', true);
$button_txt = get_post_meta($post->ID, 'wodx_form_btn_link_txt', true);
$button_link = get_post_meta($post->ID, 'wodx_form_btn_link', true);
$form_list = get_post_meta($post->ID, 'wodx_form_list', true);
$welcome_email = get_post_meta($post->ID, 'wodx_form_welcome_email', true);
$email_title = get_post_meta($post->ID, 'wodx_form_welcome_email_title', true);
$redirection_url = get_post_meta($post->ID, 'wodx_form_redirection_url', true);
$success_txt = get_post_meta($post->ID, 'wodx_form_success_txt', true);
?>
<div style="display: flex; justify-content:flex-start; flex-wrap: wrap; align-items: baseline;">
<div id="wodx-steps">
    <?php
    if (empty($steps)) {
        // Default first step if no steps are defined
        $steps = array(
            1 => array(
                'title' => '',
                'description' => '',
                'subtitle' => '',
                'options' => ''
            )
        );
    }
    foreach ($steps as $index => $step) :
?>
    <div class="step" data-step="<?php echo $index; ?>">
        <h3>Step <?php echo $index; ?></h3>
        <div style="display: flex; flex-direction: column">
            <label>Title:</label>
            <input type="text" name="wodx_form_steps[<?php echo $index; ?>][title]" value="<?php echo esc_attr($step['title']); ?>"><br>
        </div>
        <div style="display: flex; flex-direction: column">
            <label>Description:</label>
            <?php
            $settings = array(
                'textarea_name' => "wodx_form_steps[$index][description]",
                'textarea_rows' => 5,
                'media_buttons' => false
            );
            wp_editor(htmlspecialchars_decode($step['description']), "wodx_form_steps_{$index}_description", $settings);
            ?>
        </div>
        <div style="display: flex; flex-direction: column">
            <label>Subtitle:</label>
            <input type="text" name="wodx_form_steps[<?php echo $index; ?>][subtitle]" value="<?php echo esc_attr($step['subtitle']); ?>"><br>
        </div>
        <div style="display: flex; flex-direction: column">
            <label>Options (comma separated):</label>
            <input type="text" name="wodx_form_steps[<?php echo $index; ?>][options]" value="<?php echo esc_attr($step['options']); ?>">
        </div>
        <div style="display: flex; flex-direction: column">
            <label>Redirect URL (optional):</label>
            <input type="url" name="wodx_form_steps[<?php echo $index; ?>][redirect_url]" value="<?php echo isset($step['redirect_url']) ? esc_url($step['redirect_url']) : ''; ?>">
        </div>
    </div>
<?php endforeach; ?>

    <br>
    <button id="add-step">Add Step</button>
    <button id="delete-step">Delete Last Step</button>
</div>

<div id="wodx-final" style="margin-left: 15px;">
    <h3>Form settings</h3>
    <div style="display: flex; flex-direction: column">
        <label>Final Title:</label>
        <input type="text" name="wodx_form_final_title" value="<?php echo esc_attr($final_title); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Final Text:</label>
        <?php
        $settings = array(
            'textarea_name' => 'wodx_form_final_text',
            'textarea_rows' => 5,
            'media_buttons' => false
        );
        wp_editor(htmlspecialchars_decode($final_text), 'wodx_form_final_text_editor', $settings);
        ?>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Button text:</label>
        <input type="text" name="wodx_form_btn_link_txt" value="<?php echo esc_attr($button_txt); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Button link:</label>
        <input type="text" name="wodx_form_btn_link" value="<?php echo esc_attr($button_link); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Newsletter list:</label>
        <input type="text" name="wodx_form_list" value="<?php echo esc_attr($form_list); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Welcome email subject:</label>
        <input type="text" name="wodx_form_welcome_email_title" value="<?php echo esc_attr($email_title); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Welcome email (HTML):</label>
        <?php
        $settings = array(
            'textarea_name' => 'wodx_form_welcome_email',
            'textarea_rows' => 5,
            'media_buttons' => false
        );
        wp_editor(htmlspecialchars_decode($welcome_email), 'wodx_form_welcome_email_editor', $settings);
        ?>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Redirection URL:</label>
        <input type="url" name="wodx_form_redirection_url" value="<?php echo esc_attr($redirection_url); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>Success txt:</label>
        <input type="text" name="wodx_form_success_txt" value="<?php echo esc_attr($success_txt); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
    <label>WhatsApp Share Message:</label>
    	<textarea name="wodx_form_whatsapp_share_message" rows="5"><?php echo esc_textarea($whatsapp_share_message); ?></textarea><br>
	</div>
    <div style="display: flex; flex-direction: column">
        <label>WhatsApp Share Count:</label>
        <input type="number" name="wodx_form_whatsapp_share_count" value="<?php echo esc_attr($whatsapp_share_count); ?>"><br>
    </div>
    <div style="display: flex; flex-direction: column">
        <label>WhatsApp Button Image URL:</label>
        <input type="text" name="wodx_form_whatsapp_button_image" value="<?php echo esc_attr($whatsapp_button_image); ?>"><br>
    </div>
</div>




<script>
    jQuery(document).ready(function($) {
        // Function to initialize TinyMCE on all textareas
        function initializeTinyMCE(selector) {
            $(selector).each(function() {
                var id = $(this).attr('id');
                if (typeof tinymce !== 'undefined') {
                    tinymce.execCommand('mceRemoveEditor', true, id);
                    tinymce.execCommand('mceAddEditor', true, id);
                }
            });
        }

        // Initialize TinyMCE on document ready
        initializeTinyMCE('textarea.wp-editor-area');

        $('#add-step').on('click', function(e) {
            e.preventDefault();
            var stepCount = $('#wodx-steps').children('.step').length + 1;
            var newStep = `
                <div class="step" data-step="${stepCount}">
                    <h3>Step ${stepCount}</h3>
                    <div style="display: flex; flex-direction: column">
                        <label>Title:</label>
                        <input type="text" name="wodx_form_steps[${stepCount}][title]"><br>
                    </div>
                    <div style="display: flex; flex-direction: column">
                        <label>Description:</label>
                        <div class="wp-editor-wrap">
                            <div id="wodx_form_steps_${stepCount}_description_editor" class="wp-editor-container">
                                <textarea id="wodx_form_steps_${stepCount}_description" name="wodx_form_steps[${stepCount}][description]" class="wp-editor-area"></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column">
                        <label>Subtitle:</label>
                        <input type="text" name="wodx_form_steps[${stepCount}][subtitle]"><br>
                    </div>
                    <div style="display: flex; flex-direction: column">
                        <label>Options (comma separated):</label>
                        <input type="text" name="wodx_form_steps[${stepCount}][options]">
                    </div>
                    <div style="display: flex; flex-direction: column">
                        <label>Redirect URL (optional):</label>
                        <input type="url" name="wodx_form_steps[${stepCount}][redirect_url]">
                    </div>
                </div>
            `;
            $('#wodx-steps').append(newStep);
            initializeTinyMCE(`#wodx_form_steps_${stepCount}_description`); // Initialize TinyMCE for the new textarea
            wp.editor.initialize('wodx_form_steps_' + stepCount + '_description', {
                tinymce: {
                    wpautop: true,
                    plugins: 'lists,paste,wordpress',
                    toolbar1: 'bold,italic,underline,block,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,dfw,wp_adv',
                    toolbar2: 'formatselect,underline,alignjustify,bullist,numlist,outdent,indent,undo,redo,wp_help'
                },
                quicktags: true
            });
        });

        $('#delete-step').on('click', function(e) {
            e.preventDefault();
            var stepCount = $('#wodx-steps').children('.step').length;
            if (stepCount > 1) {
                $('#wodx-steps').children('.step').last().remove();
            }
        });
    });
</script>


