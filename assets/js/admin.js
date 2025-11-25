jQuery(document).ready(function($) {
    'use strict';

    // Campaign Modal
    var campaignModal = $('#campaign-modal');
    var campaignForm = $('#campaign-form');

    $('#create-campaign-btn').on('click', function(e) {
        e.preventDefault();
        campaignForm[0].reset();
        $('#campaign-id').val('');
        campaignModal.show();
    });

    $('.close, .cancel-modal').on('click', function() {
        campaignModal.hide();
    });

    $(window).on('click', function(e) {
        if ($(e.target).is(campaignModal)) {
            campaignModal.hide();
        }
    });

    // Save Campaign
    campaignForm.on('submit', function(e) {
        e.preventDefault();

        var campaignId = $('#campaign-id').val();
        var action = campaignId ? 'wao_wcr_update_campaign' : 'wao_wcr_create_campaign';

        var formData = new FormData(this);
        formData.append('action', action);
        formData.append('nonce', waoWcrAdmin.nonce);

        $.ajax({
            url: waoWcrAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Edit Campaign
    $('.edit-campaign').on('click', function(e) {
        e.preventDefault();
        var campaignId = $(this).data('id');

        $.ajax({
            url: waoWcrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wao_wcr_get_campaign',
                campaign_id: campaignId,
                nonce: waoWcrAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    var campaign = response.data.campaign;
                    $('#campaign-id').val(campaign.id);
                    $('input[name="campaign[name]"]').val(campaign.name);
                    $('input[name="campaign[review_count_required]"]').val(campaign.review_count_required);
                    $('select[name="campaign[coupon_type]"]').val(campaign.coupon_type);
                    $('input[name="campaign[coupon_amount]"]').val(campaign.coupon_amount);
                    $('input[name="campaign[coupon_validity_days]"]').val(campaign.coupon_validity_days);
                    $('input[name="campaign[usage_limit]"]').val(campaign.usage_limit);
                    $('input[name="campaign[minimum_spend]"]').val(campaign.minimum_spend);
                    $('input[name="campaign[free_shipping]"]').prop('checked', campaign.free_shipping === 'yes');
                    $('input[name="campaign[exclude_sale_items]"]').prop('checked', campaign.exclude_sale_items === 'yes');
                    $('input[name="campaign[individual_use]"]').prop('checked', campaign.individual_use === 'yes');
                    $('select[name="campaign[status]"]').val(campaign.status);
                    campaignModal.show();
                }
            }
        });
    });

    // Delete Campaign
    $('.delete-campaign').on('click', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this campaign?')) {
            return;
        }

        var campaignId = $(this).data('id');

        $.ajax({
            url: waoWcrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wao_wcr_delete_campaign',
                campaign_id: campaignId,
                nonce: waoWcrAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });

    // Send Test Email
    $('#send-test-email').on('click', function(e) {
        e.preventDefault();

        var email = $('#test-email-address').val();
        if (!email) {
            alert('Please enter an email address.');
            return;
        }

        var subject = $('input[name="subject"]').val();
        var content = $('#email_content').val();
        var headerColor = $('input[name="header_color"]').val();

        $(this).prop('disabled', true).text('Sending...');

        $.ajax({
            url: waoWcrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wao_wcr_send_test_email',
                email: email,
                subject: subject,
                content: content,
                header_color: headerColor,
                nonce: waoWcrAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message);
                }
                $('#send-test-email').prop('disabled', false).text('Send Test Email');
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $('#send-test-email').prop('disabled', false).text('Send Test Email');
            }
        });
    });

    // Tab Switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-panel').removeClass('active');
        $(target).addClass('active');
    });
});
