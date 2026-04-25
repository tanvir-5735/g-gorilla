jQuery(document).ready(function($) {

    // Salary update for regular calculator
    $(document).on("input", "#km_salary", function() {
        var val = $(this).val();
        $("#km_salary_val").text("$" + val);
    });

    // Salary update for inline calculator
    $(document).on("input", "#km_inline_salary", function() {
        var val = $(this).val();
        $("#km_inline_salary_val").text("$" + val);
    });

    // Regular calculator tabs - completely isolated
    $(".km-regular-tab").click(function() {
        // Only affect tabs within the same calculator
        var $calculator = $(this).closest(".km-calculator-wrapper");
        
        // Remove active class from all regular tabs in this calculator
        $calculator.find(".km-regular-tab").removeClass("km-active");
        
        // Add active class to clicked tab
        $(this).addClass("km-active");
        
        // Reset form for regular calculator
        resetRegularForm();
    });

    // Inline calculator tabs - completely isolated
    $(".km-inline-tab").click(function() {
        // Only affect tabs within the same inline calculator
        var $wrapper = $(this).closest(".km-inline-wrapper");
        
        // Remove active class from all inline tabs in this wrapper
        $wrapper.find(".km-inline-tab").removeClass("km-active");
        
        // Add active class to clicked tab
        $(this).addClass("km-active");
        
        // Reset inline form
        resetInlineForm($wrapper);
    });

    // Calculate button for regular calculator
    $("#km_calculate_btn").click(function() {
        var salary = parseFloat($("#km_salary").val());
        var years = parseFloat($("#km_years").val());

        if (!years) {
            alert("Please select number of years");
            return;
        }

        var refund = salary * years * 0.04;
        var formatted = "$" + refund.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        $("#km_refund_amount").text(formatted);
        $("#km-form-content").addClass("hide");

        setTimeout(function() {
            $("#km-result-content").addClass("show");
        }, 300);
    });

    // Clear button for regular calculator
    $("#km_clear_btn").click(function() {
        $("#km-result-content").removeClass("show");

        setTimeout(function() {
            $("#km-form-content").removeClass("hide");
            resetRegularForm();
        }, 600);
    });

    // Clear button for inline calculator
    $(document).on("click", "#km_inline_clear", function() {
        var $wrapper = $(this).closest(".km-inline-wrapper");
        resetInlineForm($wrapper);
    });

    // Inline auto calculation
    $(document).on("input change", ".km-inline-industry, .km-inline-years, #km_inline_salary", function() {
        var $wrapper = $(this).closest(".km-inline-wrapper");
        
        if (!$wrapper.length) return; // Exit if not in inline wrapper

        var salary = parseFloat($wrapper.find("#km_inline_salary").val());
        var years = parseFloat($wrapper.find(".km-inline-years").val());

        $wrapper.find("#km_inline_salary_val").text("$" + salary);

        if (!years || isNaN(years)) {
            $wrapper.find(".km-inline-result").text("$0");
            return;
        }

        var refund = salary * years * 0.04;
        var formatted = "$" + refund.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        $wrapper.find(".km-inline-result").text(formatted);
    });

    // Reset function for regular calculator
    function resetRegularForm() {
        $("#km_salary").val(18000);
        $("#km_salary_val").text("$18000");
        $("#km_years").val("");
        $("#km_industry").val("");
    }

    // Reset function for inline calculator
    function resetInlineForm($wrapper) {
        $wrapper.find("#km_inline_salary").val(18000);
        $wrapper.find("#km_inline_salary_val").text("$18000");
        $wrapper.find(".km-inline-years").val("");
        $wrapper.find(".km-inline-industry").val("");
        $wrapper.find(".km-inline-result").text("$0");
    }

});