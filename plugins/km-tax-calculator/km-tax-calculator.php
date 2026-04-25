<?php
/*
Plugin Name: Tax Refund Calculator
Description: Tax Refund Calculator Shortcode [km_tax_calculator] and [km_tax_calculator_inline] + Modal Claim Form
Version: 2.2
Author: Md. Kamruzzaman
*/

if (!defined('ABSPATH')) {
    exit;
}

define('KM_TC_URL', plugin_dir_url(__FILE__));
define('KM_TC_VERSION', '2.2');

// Enqueue assets (with AJAX object for modal)
function km_tc_assets() {
    wp_enqueue_style('km-tc-style', KM_TC_URL . 'css/km-style.css', array(), KM_TC_VERSION);
    wp_enqueue_script('km-tc-script', KM_TC_URL . 'js/km-script.js', array('jquery'), KM_TC_VERSION, true);
    wp_localize_script('km-tc-script', 'km_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('km_claim_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'km_tc_assets');

// ==================== REGULAR CALCULATOR (exactly as your original) ====================
function km_tax_calculator_shortcode() {
    ob_start();
    ?>
    <div class="km-calculator-wrapper">
        <div class="km-card">

            <!-- Form Content -->
            <div class="km-content km-form-content" id="km-form-content">
                <div class="km-tabs km-regular-tabs">
                    <button type="button" class="km-tab km-regular-tab km-active" data-tab="paye">PAYE Tax Refund</button>
                    <button type="button" class="km-tab km-regular-tab" data-tab="cis">CIS Tax Refund</button>
                </div>

                <div class="km-field">
                    <label for="km_industry">Your Industry*</label>
                    <select id="km_industry" name="km_industry">
                        <option value="">Select</option>
                        <option value="construction-trades">Construction & Trades</option>
                        <option value="healthcare-care">Healthcare & Care</option>
                        <option value="recruitment">Recruitment</option>
                        <option value="travel-airlines">Travel & Airlines</option>
                        <option value="public-sector">Public Sector</option>
                        <option value="property-real-estate">Property & Real Estate</option>
                        <option value="other-industry">Other Industry</option>
                    </select>
                </div>

                <div class="km-field">
                    <label for="km_years">Number of years*</label>
                    <select id="km_years" name="km_years">
                        <option value="">Select years</option>
                        <option value="1">1 Year</option>
                        <option value="2">2 Years</option>
                        <option value="3">3 Years</option>
                        <option value="4">4 Years (Maximum)</option>
                    </select>
                </div>

                <div class="km-field">
                    <label for="km_salary">Your Salary*</label>
                    <input type="range" id="km_salary" name="km_salary" min="18000" max="150000" value="18000" step="1000">
                    <div class="km-range-footer">
                        <span id="km_salary_val">$18000</span>
                        <span class="km-range-limit">$18,000 to $150,000 Max</span>
                    </div>
                </div>

                <button type="button" id="km_calculate_btn" class="km-btn">Calculate Your Tax</button>
            </div>

            <!-- Result Content -->
            <div class="km-content km-result-content" id="km-result-content">
                <div class="result-inner">
                    <h2>Your Estimated* Refund is:</h2>
                    <div class="refund-amount" id="km_refund_amount">$0</div>
                    <p class="disclaimer">
                        <span class="info-icon">ℹ</span>
                        *Calculation is an estimation based on the average claim amount for your salary and taxable allowance. Contact us today for an exact refund figure.
                    </p>
                    <!-- MODAL TRIGGER (was a link to /sign-up/) -->
                    <a href="javascript:void(0);" class="km-claim-btn">Start Your Claim</a>
                    <a href="javascript:void(0);" id="km_clear_btn" class="km-clear-link">Back to Calculator</a>
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('km_tax_calculator', 'km_tax_calculator_shortcode');

// ==================== INLINE CALCULATOR (exactly as your original, with mascot image) ====================
function km_tax_calculator_inline_shortcode() { 
    ob_start();
    ?>
    <div class="km-inline-wrapper">

        <!-- Two-column layout -->
        <div class="km-inline-container">

            <!-- Left: Calculator Form -->
            <div class="km-inline-left">
                <div class="km-card km-no-border">

                    <!-- Header Text - exactly above calculator -->
                    <div class="km-inline-header">
                        <div class="tr-cal">
                            <h2>Tax Refund Calculator</h2>
                        </div>
                        <p class="km-header-subtitle">Calculate your refund</p>
                        <p class="km-header-description">
                            Calculate your potential tax rebate in seconds. Many UK workers unknowingly overpay tax every year. Enter a few details to see how much you could claim back.
                        </p>
                    </div>

                    <!-- Tabs with unique class for inline version -->
                    <div class="km-tabs km-inline-tabs">
                        <button type="button" class="km-tab km-inline-tab km-active" data-tab="paye">PAYE Tax Refund</button>
                        <button type="button" class="km-tab km-inline-tab" data-tab="cis">CIS Tax Refund</button>
                    </div>

                    <!-- Fields -->
                    <div class="km-field">
                        <label>Your Industry*</label>
                        <select class="km-inline-input km-inline-industry">
                            <option value="">Select</option>
                            <option value="construction-trades">Construction & Trades</option>
                            <option value="healthcare-care">Healthcare & Care</option>
                            <option value="recruitment">Recruitment</option>
                            <option value="travel-airlines">Travel & Airlines</option>
                            <option value="public-sector">Public Sector</option>
                            <option value="property-real-estate">Property & Real Estate</option>
                            <option value="other-industry">Other Industry</option>
                        </select>
                    </div>

                    <div class="km-field">
                        <label>Number of years*</label>
                        <select class="km-inline-input km-inline-years">
                            <option value="">Select years</option>
                            <option value="1">1 Year</option>
                            <option value="2">2 Years</option>
                            <option value="3">3 Years</option>
                            <option value="4">4 Years (Maximum)</option>
                        </select>
                    </div>

                    <!-- Updated Salary Field - Matching the regular calculator design -->
                    <div class="km-field">
                        <label for="km_inline_salary">Your Salary*</label>
                        <input type="range" id="km_inline_salary" class="km-inline-salary" min="18000" max="150000" value="18000" step="1000">
                        <div class="km-range-footer">
                            <span id="km_inline_salary_val" class="km-inline-salary-val">$18000</span>
                            <span class="km-range-limit">$18,000 to $150,000 Max</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right: Result Area - thick border, button, clear link -->
            <div class="km-inline-right">
                <div class="km-result-box">
                    <h3>Your Estimated* Refund is:</h3>
                    <div class="refund-amount km-inline-result">$0</div>
                    <p class="disclaimer-inline">
                        *Calculation is an estimation based on the average claim amount for your salary and taxable allowance. Contact us today for an exact refund figure.
                    </p>
                    <!-- MODAL TRIGGER (was a link to /sign-up/) -->
                    <a href="javascript:void(0);" class="km-claim-btn">Start Your Claim</a>
                    <a href="javascript:void(0);" id="km_inline_clear" class="km-clear-link">Clear Calculator</a><br>
                    <!-- Mascot image – using your absolute URL from original -->
                    <img src="http://localhost/g-gorilla/wp-content/uploads/2026/04/mascot-superhero-sitting.png" alt="Tax Refund" class="bottom-image">
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('km_tax_calculator_inline', 'km_tax_calculator_inline_shortcode');

// ==================== MODAL POPUP (your claim form with background & thank-you image) ====================
function km_claim_modal_html() {
    // Use the exact images you provided
    $bg_image = home_url('/wp-content/uploads/2026/04/bg-4-1.png');
    $thankyou_image = home_url('/wp-content/uploads/2026/04/image-1.png'); // adjust extension if needed
    ?>
    <div id="kmClaimModal" class="km-modal" style="display:none;">
        <div class="km-modal-content" style="background-image: url('<?php echo $bg_image; ?>'); background-size: cover; background-position: center;">
            <span class="km-modal-close">&times;</span>
            <div id="kmModalFormContainer">
                <h1 style="font-size:1.8rem; margin-top:0;">Get in touch to start your claim with Gorilla Tax Rebates</h1>
                <form id="kmClaimForm" class="km-claim-form">
                    <div class="km-form-layout">
                        <div class="double-field">
                            <div class="field-group">
                                <span class="field-label">First Name <span class="required-asterisk">*</span></span>
                                <input type="text" name="first_name" placeholder="e.g. Michael" required>
                            </div>
                            <div class="field-group">
                                <span class="field-label">Last Name <span class="required-asterisk">*</span></span>
                                <input type="text" name="last_name" placeholder="e.g. Adeyemi" required>
                            </div>
                        </div>
                        <div class="double-field">
                            <div class="field-group">
                                <span class="field-label">Email <span class="required-asterisk">*</span></span>
                                <input type="email" name="email" placeholder="you@example.com" required>
                            </div>
                            <div class="field-group">
                                <span class="field-label">Phone Number <span class="required-asterisk">*</span></span>
                                <input type="tel" name="phone" placeholder="07123 456789" required>
                            </div>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Are you an employed (PAYE) or CIS worker in the UK? <span class="required-asterisk">*</span></span>
                            <div class="radio-stack">
                                <label class="radio-option"><input type="radio" name="worker_type" value="PAYE" required> PAYE</label>
                                <label class="radio-option"><input type="radio" name="worker_type" value="CIS" required> CIS</label>
                            </div>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Do you earn over £18,000 a year? <span class="required-asterisk">*</span></span>
                            <div class="radio-stack">
                                <label class="radio-option"><input type="radio" name="income_over_18k" value="Yes" required> Yes</label>
                                <label class="radio-option"><input type="radio" name="income_over_18k" value="No" required> No</label>
                            </div>
                        </div>
                        <div class="field-group">
                            <span class="field-label">In the last 4 years, have you travelled to more than one location for the purpose of work? <span class="required-asterisk">*</span></span>
                            <div style="font-size:0.85rem; color:#4a6579; margin-bottom:6px;">For example: Another office / site for meetings or training etc.</div>
                            <div class="radio-stack">
                                <label class="radio-option"><input type="radio" name="travel_multiple" value="Yes" required> Yes</label>
                                <label class="radio-option"><input type="radio" name="travel_multiple" value="No" required> No</label>
                            </div>
                        </div>
                        <div class="field-group">
                            <span class="field-label">What industry do you work in? <span class="required-asterisk">*</span></span>
                            <div class="industry-checkbox-vertical">
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Airlines" id="ind_airlines"> <label for="ind_airlines">Airlines</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Care" id="ind_care"> <label for="ind_care">Care</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Construction" id="ind_construction"> <label for="ind_construction">Construction</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Engineering" id="ind_engineering"> <label for="ind_engineering">Engineering</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Logistics" id="ind_logistics"> <label for="ind_logistics">Logistics</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Military" id="ind_military"> <label for="ind_military">Military</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Recruitment" id="ind_recruitment"> <label for="ind_recruitment">Recruitment</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Sales" id="ind_sales"> <label for="ind_sales">Sales</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Security" id="ind_security"> <label for="ind_security">Security</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Trades & Labour" id="ind_trades"> <label for="ind_trades">Trades & Labour</label></div>
                                <div class="industry-check-row"><input type="checkbox" name="industry[]" value="Hospitality" id="ind_hospitality"> <label for="ind_hospitality">Hospitality</label></div>
                            </div>
                            <div id="kmModalIndustryError" class="km-error" style="display:none;">Please select at least one industry.</div>
                        </div>
                        <div class="consent-box">
                            <div class="consent-header">Gorilla Tax Rebates Limited is committed to protecting and respecting your privacy. Please tick below:</div>
                            <label class="checkbox-line"><input type="checkbox" name="marketing_consent" value="yes"> <span>I would like to receive future communications and marketing.</span></label>
                            <label class="checkbox-line"><input type="checkbox" name="data_consent" value="yes" required> <span class="required-checkbox">I agree to allow Gorilla Tax Rebates Limited to store and process my personal data. <span class="required-asterisk">*</span></span></label>
                        </div>
                        <button type="submit" class="km-submit-claim">Send</button>
                        <div class="km-form-status"></div>
                    </div>
                </form>
            </div>
            <div id="kmModalThankyou" style="display:none; text-align:center;">
                <div class="character_illustration">
                    <img src="<?php echo $thankyou_image; ?>" alt="Thank you" style="max-width:180px; border-radius:20px;">
                </div>
                <div class="thankyou-title">Thank you!</div>
               
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'km_claim_modal_html');

// ==================== AJAX HANDLER (submit claim & email) ====================
function km_handle_claim_submission() {
    check_ajax_referer('km_claim_nonce', 'nonce');
    
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['phone']) ||
        empty($_POST['worker_type']) || empty($_POST['income_over_18k']) || empty($_POST['travel_multiple']) ||
        empty($_POST['industry']) || empty($_POST['data_consent'])) {
        wp_send_json_error('Please fill all required fields.');
    }
    
    $industries = array_map('sanitize_text_field', (array)$_POST['industry']);
    if (empty($industries)) {
        wp_send_json_error('Please select at least one industry.');
    }
    
    $data = array(
        'first_name'      => sanitize_text_field($_POST['first_name']),
        'last_name'       => sanitize_text_field($_POST['last_name']),
        'email'           => sanitize_email($_POST['email']),
        'phone'           => sanitize_text_field($_POST['phone']),
        'worker_type'     => sanitize_text_field($_POST['worker_type']),
        'income_over_18k' => sanitize_text_field($_POST['income_over_18k']),
        'travel_multiple' => sanitize_text_field($_POST['travel_multiple']),
        'industry'        => implode(', ', $industries),
        'marketing'       => isset($_POST['marketing_consent']) ? 'Yes' : 'No',
        'data_consent'    => 'Yes',
        'estimated_refund'=> isset($_POST['estimated_refund']) ? sanitize_text_field($_POST['estimated_refund']) : '',
        'submitted'       => current_time('mysql')
    );
    
    // Store as custom post type (optional)
    wp_insert_post(array(
        'post_title'   => $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['email'],
        'post_type'    => 'tax_lead',
        'post_status'  => 'private',
        'meta_input'   => $data
    ));
    
    // Send email to admin
    $admin_email = get_option('admin_email');
    $subject = 'New Tax Refund Claim from ' . $data['first_name'] . ' ' . $data['last_name'];
    $message = "Name: {$data['first_name']} {$data['last_name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nWorker: {$data['worker_type']}\nIncome >18k: {$data['income_over_18k']}\nTravel multiple: {$data['travel_multiple']}\nIndustry: {$data['industry']}\nEstimated refund: {$data['estimated_refund']}\nMarketing consent: {$data['marketing']}\nData consent: Yes\n";
    wp_mail($admin_email, $subject, $message);
    
    // Auto-reply to user
    wp_mail($data['email'], 'Your claim request received', "Thank you {$data['first_name']}, we'll contact you shortly.");
    
    wp_send_json_success('Claim submitted successfully');
}
add_action('wp_ajax_km_submit_claim', 'km_handle_claim_submission');
add_action('wp_ajax_nopriv_km_submit_claim', 'km_handle_claim_submission');

// Register custom post type for leads
function km_register_lead_cpt() {
    register_post_type('tax_lead', array(
        'labels'      => array('name' => 'Tax Leads', 'singular_name' => 'Lead'),
        'public'      => false,
        'show_ui'     => true,
        'supports'    => array('title', 'custom-fields'),
    ));
}
add_action('init', 'km_register_lead_cpt');
?>