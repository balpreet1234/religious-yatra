<?php
	if(defined('TRIPGO_URL') 	== false) 	define('TRIPGO_URL', get_template_directory());
	if(defined('TRIPGO_URI') 	== false) 	define('TRIPGO_URI', get_template_directory_uri());

	load_theme_textdomain( 'tripgo', TRIPGO_URL . '/languages' );

	// Main Feature
	require_once( TRIPGO_URL.'/inc/class-main.php' );

	// Functions
	require_once( TRIPGO_URL.'/inc/functions.php' );

	// Hooks
	require_once( TRIPGO_URL.'/inc/class-hook.php' );

	// Widget
	require_once (TRIPGO_URL.'/inc/class-widgets.php');
	

	// Elementor
	if (defined('ELEMENTOR_VERSION')) {
		require_once (TRIPGO_URL.'/inc/class-elementor.php');
	}
	
	// WooCommerce
	if (class_exists('WooCommerce')) {
		require_once (TRIPGO_URL.'/inc/class-woo.php');
		require_once (TRIPGO_URL.'/inc/class-woo-template-functions.php');
		require_once (TRIPGO_URL.'/inc/class-woo-template-hooks.php');
	}
	
	
	/* Customize */
	if( current_user_can('customize') ){
	    require_once TRIPGO_URL.'/customize/custom-control/google-font.php';
	    require_once TRIPGO_URL.'/customize/custom-control/heading.php';
	    require_once TRIPGO_URL.'/inc/class-customize.php';
	}
    
   
	require_once ( TRIPGO_URL.'/install-resource/active-plugins.php' );
	

	
	function enqueue_custom_scripts() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            const formattedDate = `${yyyy}-${mm}-${dd}`;
            document.getElementById('check-out').min = formattedDate;
        });
    </script>
    <?php
}
add_action('wp_footer', 'enqueue_custom_scripts');

function enqueue_custom_date_scripts() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            const yyyy = today.getFullYear();
            const formattedDate = `${yyyy}-${mm}-${dd}`;
            document.getElementById('check-in').min = formattedDate;
        });
    </script>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var phoneField = document.getElementById('phone');
		phoneField.setAttribute('maxlength', '10'); // Set maxlength to 10 characters
		phoneField.addEventListener('input', function() {
			// Ensure only numbers are allowed in the input
			this.value = this.value.replace(/[^0-9]/g, '');
		});
	});
	</script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var form = document.querySelector('.wpcf7 form');

			form.addEventListener('submit', function(event) {
				var origin = document.getElementById('origin');
				var destination = document.getElementById('destination');

				if (origin.value === destination.value) {
					alert("Origin and Destination cannot be the same. Please choose different locations.");
					origin.setCustomValidity("Please choose different locations.");
					event.preventDefault(); // Prevent form submission
					return false;
				} else {
					origin.setCustomValidity(""); // Clear the validation error
				}
			});
		});
	</script>
	

    <?php
}
add_action('wp_footer', 'enqueue_custom_date_scripts');

function hook_javascript() {
    ?>
        <meta name="google-site-verification" content="6FUQ3-11TM3PRpgSksM1ctjwtj3ULGJdFY2PCkx3DJc" /> 
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1X22C5XJXJ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1X22C5XJXJ');
</script>
    <?php
}
add_action('wp_head', 'hook_javascript');



add_action( 'wp_head', 'wp_custom_validation_cf7_script' );

function wp_custom_validation_cf7_script() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            let formElems = document.getElementsByClassName('wpcf7-form');
            let submitBtns = document.getElementsByClassName('wpcf7-submit');

            // Check if form elements and submit buttons are defined
            if (formElems.length === 0 || submitBtns.length === 0) {
                return;
            }
            
            const form = formElems[0]; // Get the first contact form found on the page
            const submitBtn = submitBtns[0]; // Get the first submit button found

            // Disable the submit button on page load
            submitBtn.setAttribute('disabled', 'disabled');

            const requiredInputs = form.querySelectorAll('[aria-required="true"]');
            const originInput = document.getElementById('origin');
            const destinationInput = document.getElementById('destination');

            function checkInputs() {
                let allFilled = true;

                requiredInputs.forEach(function(input) {
                    if (input.value.trim() === '') {
                        allFilled = false;
                    }
                });

                // Enable or disable the submit button based on input validation
                if (allFilled && checkLocation()) {
                    submitBtn.removeAttribute('disabled');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                }
            }

            function checkLocation() {
                if (originInput.value.trim() === destinationInput.value.trim()) {
                    alert("Origin and Destination cannot be the same. Please choose different locations.");
                    return false; // Return false if the locations are the same
                }
                return true; // Return true if the locations are different
            }

            // Add input event listeners to required inputs
            requiredInputs.forEach(function(input) {
                input.addEventListener('input', checkInputs);
            });

            if (originInput && destinationInput) {
                originInput.addEventListener('input', checkInputs); // Check inputs for origin change
                destinationInput.addEventListener('input', checkInputs); // Check inputs for destination change
            }

            // Prevent form submission on Enter key if validation fails
            form.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    if (submitBtn.hasAttribute('disabled')) {
                        event.preventDefault(); // Prevent form submission
                        alert("Please fill out all required fields correctly before submitting.");
                    }
                }
            });
        });
    </script>
    <?php
}



