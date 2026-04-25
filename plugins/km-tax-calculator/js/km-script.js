jQuery(document).ready(function($) {

    // ========================================
    // REGULAR CALCULATOR FUNCTIONALITY
    // ========================================
    
    $(document).on("input", "#km_salary", function() {
        var val = parseInt($(this).val());
        $("#km_salary_val").text("$" + val.toLocaleString());
    });

    $(".km-regular-tab").click(function() {
        var $calculator = $(this).closest(".km-calculator-wrapper");
        $calculator.find(".km-regular-tab").removeClass("km-active");
        $(this).addClass("km-active");
        $calculator.data("active-tab", $(this).data("tab"));
        resetRegularForm($calculator);
        var $formContent = $calculator.find("#km-form-content");
        var $resultContent = $calculator.find("#km-result-content");
        if ($resultContent.hasClass("show")) {
            $resultContent.removeClass("show");
            setTimeout(function() { $formContent.removeClass("hide"); }, 300);
        }
    });

    $("#km_calculate_btn").click(function() {
        var $calculator = $(this).closest(".km-calculator-wrapper");
        var salary = parseFloat($("#km_salary").val());
        var years = parseFloat($("#km_years").val());
        var industry = $("#km_industry").val();
        var activeTab = $calculator.data("active-tab") || "paye";
        if (!industry) { alert("Please select your industry"); return; }
        if (!years || isNaN(years)) { alert("Please select number of years"); return; }
        if (!salary || salary < 18000) { alert("Please enter a valid salary (minimum £18,000)"); return; }
        var refund = calculateRefundAmount(salary, years, industry, activeTab);
        $("#km_refund_amount").text("$" + refund.toLocaleString());
        $("#km-form-content").addClass("hide");
        setTimeout(function() { $("#km-result-content").addClass("show"); }, 300);
    });

    $("#km_clear_btn").click(function() {
        $("#km-result-content").removeClass("show");
        setTimeout(function() {
            $("#km-form-content").removeClass("hide");
            resetRegularForm();
        }, 300);
    });

    function resetRegularForm($calculator) {
        $("#km_salary").val(18000);
        $("#km_salary_val").text("$18,000");
        $("#km_years").val("");
        $("#km_industry").val("");
        $("#km_refund_amount").text("$0");
    }

    // ========================================
    // INLINE CALCULATOR FUNCTIONALITY
    // ========================================
    
    $(document).on("input", "#km_inline_salary", function() {
        var val = parseInt($(this).val());
        $(this).closest(".km-field").find("#km_inline_salary_val").text("$" + val.toLocaleString());
    });

    $(".km-inline-tab").click(function() {
        var $wrapper = $(this).closest(".km-inline-wrapper");
        $wrapper.find(".km-inline-tab").removeClass("km-active");
        $(this).addClass("km-active");
        $wrapper.data("active-tab", $(this).data("tab"));
        calculateInlineRefund($wrapper);
    });

    $(document).on("input change", ".km-inline-industry, .km-inline-years, #km_inline_salary", function() {
        var $wrapper = $(this).closest(".km-inline-wrapper");
        if ($wrapper.length) calculateInlineRefund($wrapper);
    });

    function calculateInlineRefund($wrapper) {
        var salary = parseFloat($wrapper.find("#km_inline_salary").val());
        var years = parseFloat($wrapper.find(".km-inline-years").val());
        var industry = $wrapper.find(".km-inline-industry").val();
        var activeTab = $wrapper.data("active-tab") || "paye";
        $wrapper.find("#km_inline_salary_val").text("$" + salary.toLocaleString());
        if (!years || isNaN(years) || !industry || !salary) {
            $wrapper.find(".km-inline-result").text("$0");
            return;
        }
        var refund = calculateRefundAmount(salary, years, industry, activeTab);
        $wrapper.find(".km-inline-result").text("$" + refund.toLocaleString());
    }

    $(document).on("click", "#km_inline_clear", function() {
        var $wrapper = $(this).closest(".km-inline-wrapper");
        resetInlineForm($wrapper);
    });

    function resetInlineForm($wrapper) {
        $wrapper.find("#km_inline_salary").val(18000);
        $wrapper.find("#km_inline_salary_val").text("$18,000");
        $wrapper.find(".km-inline-years").val("");
        $wrapper.find(".km-inline-industry").val("");
        $wrapper.find(".km-inline-result").text("$0");
        $wrapper.find(".km-inline-tab").removeClass("km-active");
        $wrapper.find(".km-inline-tab[data-tab='paye']").addClass("km-active");
        $wrapper.data("active-tab", "paye");
    }

    // ========================================
    // COMMON CALCULATION FUNCTION
    // ========================================
    
    function calculateRefundAmount(salary, years, industry, tabType) {
        let basePercentage = 0.04;
        if (salary <= 25000) basePercentage = 0.045;
        else if (salary <= 50000) basePercentage = 0.042;
        else if (salary <= 75000) basePercentage = 0.04;
        else if (salary <= 100000) basePercentage = 0.038;
        else basePercentage = 0.035;
        let refund = salary * basePercentage * years;
        switch(industry) {
            case 'construction-trades': refund *= 1.12; break;
            case 'healthcare-care': refund *= 1.05; break;
            case 'recruitment': refund *= 1.08; break;
            case 'travel-airlines': refund *= 1.1; break;
            case 'public-sector': refund *= 1.03; break;
            case 'property-real-estate': refund *= 1.07; break;
        }
        if (tabType === 'cis') refund *= 1.15;
        if (refund < 500 && refund > 0) refund = 500;
        return Math.round(refund);
    }

    // ========================================
    // MODAL POPUP LOGIC (with validation + background scroll lock)
    // ========================================
    
    var $modal = $("#kmClaimModal");
    var $formContainer = $("#kmModalFormContainer");
    var $thankyou = $("#kmModalThankyou");

    // Open modal when clicking claim button – with validation
    $(document).off("click", ".km-claim-btn").on("click", ".km-claim-btn", function(e) {
        e.preventDefault();

        // Determine which calculator we're in
        var $calculator = $(this).closest(".km-calculator-wrapper");
        var $inline = $(this).closest(".km-inline-wrapper");
        var isInline = $inline.length > 0;

        var industry, years, salary;

        if (isInline) {
            industry = $inline.find(".km-inline-industry").val();
            years = $inline.find(".km-inline-years").val();
            salary = $inline.find("#km_inline_salary").val();
        } else {
            industry = $("#km_industry").val();
            years = $("#km_years").val();
            salary = $("#km_salary").val();
        }

        // Validation
        if (!industry) {
            alert("Please select your industry");
            return;
        }
        if (!years || years === "") {
            alert("Please select number of years");
            return;
        }
        if (!salary || parseFloat(salary) < 18000) {
            alert("Please enter a valid salary (minimum £18,000)");
            return;
        }

        // Pass estimated refund to form
        var refund = $(this).closest(".km-calculator-wrapper, .km-inline-wrapper")
            .find(".refund-amount, .km-inline-result").text();
        $("#kmClaimForm").find("input[name='estimated_refund']").remove();
        $("#kmClaimForm").append('<input type="hidden" name="estimated_refund" value="' + refund + '">');

        // Show modal with flex centering
        $modal.css('display', 'flex').hide().fadeIn(200);
        $formContainer.show();
        $thankyou.hide();

        // Reset form fields
        $("#kmClaimForm")[0].reset();
        $("#kmModalIndustryError").hide();
        $(".km-form-status").html("");

        // Lock background scroll
        $("body").addClass("km-modal-open");
    });

    // Close modal: click on X or outside the modal content
    $(".km-modal-close, .km-modal").on("click", function(e) {
        if (e.target == this || $(e.target).hasClass("km-modal-close")) {
            $modal.fadeOut(200);
            $("body").removeClass("km-modal-open");
        }
    });

    // Close modal with ESC key
    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $modal.is(":visible")) {
            $modal.fadeOut(200);
            $("body").removeClass("km-modal-open");
        }
    });

    // AJAX form submission
    $("#kmClaimForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var $status = $form.find(".km-form-status");

        if ($form.find("input[name='industry[]']:checked").length === 0) {
            $("#kmModalIndustryError").show();
            return;
        } else {
            $("#kmModalIndustryError").hide();
        }

        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        $status.html("Submitting...").css("color", "blue");

        $.ajax({
            url: km_ajax.ajax_url,
            type: "POST",
            data: $form.serialize() + "&action=km_submit_claim&nonce=" + km_ajax.nonce,
            success: function(res) {
                if (res.success) {
                    $formContainer.hide();
                    $thankyou.show();
                    $status.html("");
                } else {
                    $status.html("Error: " + res.data).css("color", "red");
                }
            },
            error: function() {
                $status.html("Server error. Please try again.").css("color", "red");
            }
        });
    });

    // ========================================
    // MOBILE OPTIMIZATION & INIT
    // ========================================
    
    if ('ontouchstart' in window) {
        $('.km-tab, .km-btn, .km-claim-btn, .km-clear-link').on('touchstart', function() { $(this).trigger('focus'); });
        $('input, select').on('focus', function() { if (window.innerWidth <= 768) document.body.style.fontSize = '16px'; });
    }
    
    $(window).on('resize', function() {
        if ($(window).width() <= 768) $('.km-content').css({ 'position': 'relative', 'height': 'auto' });
        else $('.km-content').css({ 'position': 'absolute', 'height': '' });
    });
    $(window).trigger('resize');
    
    function initCalculators() {
        if ($("#km_salary").length) { $("#km_salary").val(18000); $("#km_salary_val").text("$18,000"); }
        if ($("#km_inline_salary").length) { $("#km_inline_salary").val(18000); $("#km_inline_salary_val").text("$18,000"); }
        $(".km-regular-tab.km-active").each(function() { var $calc = $(this).closest(".km-calculator-wrapper"); $calc.data("active-tab", $(this).data("tab")); });
        $(".km-inline-tab.km-active").each(function() { var $wrapper = $(this).closest(".km-inline-wrapper"); $wrapper.data("active-tab", $(this).data("tab")); });
        $(".km-inline-wrapper").each(function() { var $w = $(this); if ($w.find(".km-inline-industry").val() && $w.find(".km-inline-years").val()) calculateInlineRefund($w); });
    }
    initCalculators();
    
    $(document).on("keypress", "#km_salary, #km_years, #km_industry", function(e) { if (e.which === 13) { e.preventDefault(); $("#km_calculate_btn").click(); } });
    $(document).on("keypress", ".km-inline-industry, .km-inline-years, #km_inline_salary", function(e) { if (e.which === 13 && $(this).closest(".km-inline-wrapper").length) { e.preventDefault(); $(this).closest(".km-inline-wrapper").find(".km-claim-btn").click(); } });
    
});