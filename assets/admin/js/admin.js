jQuery(document).ready(function (jQuery) {

    jQuery(document.body).on('click', '#generate_kigoplan_codes', function () {

        var total = jQuery('#kigoplan_general_generate_codes_amount').val();
        var year = jQuery('#kigoplan_general_generate_codes_year').val();

        alert(total);

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "generate_kigoplan_codes",
                "nonce": generateKigoplanCodesAdminJs.nonce,
                "total": total,
                "year": year
            },
            success: function (data) {
                console.log(data);

                if (data['error']) {
                    alert(data['error']);
                } else {
                   // location.reload();
                }

            },
            error: function (error) {
                console.log(error);
            }
        });

    });



});