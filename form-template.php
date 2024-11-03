<?php
$form_id = $atts['id'];
$steps = get_post_meta($form_id, 'wodx_form_steps', true);
$whatsapp_share_message = get_post_meta($form_id, 'wodx_form_whatsapp_share_message', true); // Get the WhatsApp share message
$whatsapp_share_count = get_post_meta($form_id, 'wodx_form_whatsapp_share_count', true); // Get the WhatsApp share count
$whatsapp_button_image = get_post_meta($form_id, 'wodx_form_whatsapp_button_image', true); // Get the WhatsApp button image URL
$starting_step = get_post_meta($form_id, 'wodx_form_starting_step', true);
if (!$starting_step) {
    $starting_step = 1; // Default to 1 if not set
}
if (!is_array($steps)) {
    $steps = array();
}
$step_count = count($steps);

if (!function_exists('is_image_url_check')) {
    function is_image_url_check($url) {
        $image_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        return in_array($extension, $image_extensions);
    }
}
?>

<?php if ($step_count > 0): ?>
    <form id="wodx-form">
        <div id="progress-bar">
            <div class="progress-bar-fill" style="width: 0%;"></div>
        </div>
        <div class="step-count">
            <span class="current-step"><?php echo esc_html($starting_step); ?></span> - <?php echo $step_count + 1; ?>
        </div>
        <?php foreach ($steps as $index => $step): ?>
            <div class="step" data-step="<?php echo $index; ?>" <?php if ($index + 1 == $starting_step) echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                <h2><?php echo esc_html($step['title']); ?></h2>
                <p><?php echo $step['description']; ?></p>
                <h3><?php echo esc_html($step['subtitle']); ?></h3>
                <?php $options = explode(',', $step['options']); ?>
                <div class="buttons-container">
                    <?php foreach ($options as $option): ?>
                        <button type="button" class="option" data-value="<?php echo esc_attr(trim($option)); ?>"><?php echo esc_html(trim($option)); ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" class="redirect-url" value="<?php echo esc_url($step['redirect_url']); ?>">
            </div>
        <?php endforeach; ?>
        <div class="step" data-step="<?php echo $step_count + 1; ?>" style="display: none; justify-content: flex-start;">
            <div class="final-container">
                <?php
                    $final_title = get_post_meta($form_id, 'wodx_form_final_title', true);
                    $final_txt = get_post_meta($form_id, 'wodx_form_final_text', true);
                    $newsletter_form = get_post_meta($form_id, 'wodx_form_list', true);
                    $button_txt = get_post_meta($form_id, 'wodx_form_btn_link_txt', true);
                    $button_link = get_post_meta($form_id, 'wodx_form_btn_link', true);

                    echo '<h2 class="final-title">' . esc_html($final_title) . '</h2>';
                    echo '<p class="final-txt">' . $final_txt . '</p>';
                ?>

              <?php if ($whatsapp_share_message): ?>
                <div id="share-prompt" style="margin: 0 auto;">
                    <p>Por favor comparte en whatsapp para continuar:</p>
                    <p id="remaining-shares"></p>
                    <a style="padding: 10px; color: black; font-weight: 800; border-radius: 8px; display: flex; justify-content: center; align-items: center;" id="whatsapp-share-button" href="https://api.whatsapp.com/send/?text=<?php echo urlencode($whatsapp_share_message); ?>" target="_blank">
                        <?php if ($whatsapp_button_image && is_image_url_check($whatsapp_button_image)): ?>
                            <img src="<?php echo esc_url($whatsapp_button_image); ?>" alt="Share on WhatsApp" style="height: auto; margin: 0 auto; width: 100%; max-width: 100%;">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 48 48">
                                <path fill="#fff" d="M4.9,43.3l2.7-9.8C5.9,30.6,5,27.3,5,24C5,13.5,13.5,5,24,5c5.1,0,9.8,2,13.4,5.6 C41,14.2,43,18.9,43,24c0,10.5-8.5,19-19,19c0,0,0,0,0,0h0c-3.2,0-6.3-0.8-9.1-2.3L4.9,43.3z"></path>
                                <path fill="#fff" d="M4.9,43.8c-0.1,0-0.3-0.1-0.4-0.1c-0.1-0.1-0.2-0.3-0.1-0.5L7,33.5c-1.6-2.9-2.5-6.2-2.5-9.6  C4.5,13.2,13.3,4.5,24,4.5c5.2,0,10.1,2,13.8,5.7c3.7,3.7,5.7,8.6,5.7,13.8c0,10.7-8.7,19.5-19.5,19.5c-3.2,0-6.3-0.8-9.1-2.3   L5,43.8C5,43.8,4.9,43.8,4.9,43.8z"></path>
                                <path fill="#cfd8dc" d="M24,5c5.1,0,9.8,2,13.4,5.6C41,14.2,43,18.9,43,24c0,10.5-8.5,19-19,19h0c-3.2,0-6.3-0.8-9.1-2.3   L4.9,43.3l2.7-9.8C5.9,30.6,5,27.3,5,24C5,13.5,13.5,5,24,5 M24,43L24,43L24,43 M24,43L24,43L24,43 M24,4L24,4C13,4,4,13,4,24   c0,3.4,0.8,6.7,2.5,9.6L3.9,43c-0.1,0.3,0,0.7,0.3,1c0.2,0.2,0.4,0.3,0.7,0.3c0.1,0,0.2,0,0.3,0l9.7-2.5c2.8,1.5,6,2.2,9.2,2.2  c11,0,20-9,20-20c0-5.3-2.1-10.4-5.8-14.1C34.4,6.1,29.4,4,24,4L24,4z"></path>
                                <path fill="#40c351" d="M35.2,12.8c-3-3-6.9-4.6-11.2-4.6C15.3,8.2,8.2,15.3,8.2,24c0,3,0.8,5.9,2.4,8.4L11,33l-1.6,5.8    l6-1.6l0.6,0.3c2.4,1.4,5.2,2.2,8,2.2h0c8.7,0,15.8-7.1,15.8-15.8C39.8,19.8,38.2,15.8,35.2,12.8z"></path>
                                <path fill="#fff" fill-rule="evenodd" d="M19.3,16c-0.4-0.8-0.7-0.8-1.1-0.8c-0.3,0-0.6,0-0.9,0   s-0.8,0.1-1.3,0.6c-0.4,0.5-1.7,1.6-1.7,4s1.7,4.6,1.9,4.9s3.3,5.3,8.1,7.2c4,1.6,4.8,1.3,5.7,1.2c0.9-0.1,2.8-1.1,3.2-2.3  c0.4-1.1,0.4-2.1,0.3-2.3c-0.1-0.2-0.4-0.3-0.9-0.6s-2.8-1.4-3.2-1.5c-0.4-0.2-0.8-0.2-1.1,0.2c-0.3,0.5-1.2,1.5-1.5,1.9    c-0.3,0.3-0.6,0.4-1,0.1c-0.5-0.2-2-0.7-3.8-2.4c-1.4-1.3-2.4-2.8-2.6-3.3c-0.3-0.5,0-0.7,0.2-1c0.2-0.2,0.5-0.6,0.7-0.8    c0.2-0.3,0.3-0.5,0.5-0.8c0.2-0.3,0.1-0.6,0-0.8C20.6,19.3,19.7,17,19.3,16z" clip-rule="evenodd"></path>
                            </svg> Compartir en Whatsapp
                        <?php endif; ?>
                    </a>
                </div>

                <div id="hidden-content" style="display: none; margin: 0 auto; text-align: center;">
                    <?php
                    if ($button_txt) {
                        if (is_image_url_check($button_txt)) {
                            echo '<a style="margin: 0 auto; text-align: center;" class="" target="_blank" href="' . esc_url($button_link) . '"><img src="' . esc_url($button_txt) . '" alt="Button Image" /></a>';
                        } else {
                            echo '<a class="btnAction" target="_blank" href="' . esc_url($button_link) . '">' . esc_html($button_txt) . '</a>';
                        }
                    } else {
                        echo do_shortcode($newsletter_form);
                    }
                    ?>
                </div>
                <?php endif; ?>

                <input type="hidden" name="form_id" value="<?php echo esc_attr($form_id); ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wodx_form_nonce'); ?>">
            </div>
        </div>
    </form>
    <div id="wodx-form-response" style="text-align:center;"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    (function($) {
            var startingStep = <?php echo intval($starting_step); ?>;
            var totalSteps = <?php echo $step_count + 1; ?>;
            var shareButton = document.getElementById('whatsapp-share-button');
            var hiddenContent = document.getElementById('hidden-content');
            var sharePrompt = document.getElementById('share-prompt');
            var shareCount = <?php echo json_encode($whatsapp_share_count); ?>;
            var currentShareCount = localStorage.getItem('whatsapp_share_count') || 0;
            var remainingShares = document.getElementById('remaining-shares');
            var currentStep = typeof startingStep !== 'undefined' ? startingStep : 1;
            function updateRemainingShares() {
                var sharesLeft = shareCount - currentShareCount;
                remainingShares.innerHTML = 'Te quedan ' + sharesLeft + ' veces por compartir.';
            }
            console.log(startingStep);

            var currentStep = typeof startingStep !== 'undefined' ? startingStep : 1;
            //var totalSteps = $('.step').length;
        

        function showStep(step) {
            $('.step').hide();
            $('.step[data-step="' + step + '"]').show();
            $('.progress-bar-fill').css('width', ((step / totalSteps) * 100) + '%');
            $('.current-step').text(step);
            console.log("Showing step: " + step);
        }

        // Ensure the correct step is shown on page load
        showStep(currentStep);

        // Click handler for step options
        $('.option').on('click', function() {
            var stepElement = $(this).closest('.step');
            var redirectUrl = stepElement.find('.redirect-url').val();

            if (redirectUrl && redirectUrl.length > 0) {
                window.location.href = redirectUrl;
            } else {
                currentStep++;
                if (currentStep <= totalSteps) {
                    showStep(currentStep);
                } else {
                    console.log("No more steps.");
                }
            }
        });

            // Initial update of remaining shares
            updateRemainingShares();

            // Check if the user has already shared the page enough times
            if (currentShareCount >= shareCount) {
                sharePrompt.style.display = 'none';
                hiddenContent.style.display = 'block';
            }

            shareButton.addEventListener('click', function() {
                setTimeout(function() {
                    currentShareCount++;
                    localStorage.setItem('whatsapp_share_count', currentShareCount);
                    updateRemainingShares();
                    if (currentShareCount >= shareCount) {
                        sharePrompt.style.display = 'none';
                        hiddenContent.style.display = 'block';
                    }
                }, 3000);
            });
         })(jQuery);
});

    </script>
<?php else: ?>
    <p>No steps defined for this form.</p>
<?php endif; ?>
