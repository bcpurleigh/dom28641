<?php
/** no direct access **/
defined('_MECEXEC_') or die();

// MEC Settings
$settings = $this->get_settings();

// Booking module is disabled
if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) return;

$event = $event[0];

$tickets = isset($event->data->tickets) ? $event->data->tickets : array();
$dates = isset($event->dates) ? $event->dates : $event->date;

// No Dates
if(!count($dates)) return;

// No Tickets
if(!count($tickets)) return;

// Generate JavaScript code of Booking Module
$javascript = '<script type="text/javascript">
jQuery("#mec_book_form").on("submit", function(event)
{
    event.preventDefault();
    mec_book_form_submit();
});

var mec_tickets_availability_ajax = false;
function mec_get_tickets_availability(event_id, date)
{
    // Add loading Class to the ticket list
    jQuery(".mec-event-tickets-list").addClass("loading");
    jQuery(".mec-event-tickets-list input").prop("disabled", true);
    
    // Abort previous request
    if(mec_tickets_availability_ajax) mec_tickets_availability_ajax.abort();
    
    mec_tickets_availability_ajax = jQuery.ajax(
    {
        type: "GET",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: "action=mec_tickets_availability&event_id="+event_id+"&date="+date,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the ticket list
            jQuery(".mec-event-tickets-list").removeClass("loading");
            jQuery(".mec-event-tickets-list input").prop("disabled", false);

            // Set Total Booking Limit
            if(typeof data.availability.total != "undefined") jQuery("#mec_book_form_tickets_container").data("total-booking-limit", data.availability.total);
            
            for(ticket_id in data.availability)
            {
                var limit = data.availability[ticket_id];
                
                jQuery("#mec_event_ticket"+ticket_id).addClass(".mec-event-ticket"+limit);
                
                // There are some available spots
                if(limit != "0")
                {
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").removeClass("mec-util-hidden");
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").addClass("mec-util-hidden");
                }
                // All spots are sold.
                else
                {
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").addClass("mec-util-hidden");
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").removeClass("mec-util-hidden");
                }
                
                if(limit == "-1")
                {
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", "");
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html("'.esc_html__("Unlimited", 'mec').'");
                }
                else
                {
                    var cur_count = jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val();
                    if(cur_count > limit) jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val(limit);
                    
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", limit);
                    jQuery("#mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html(limit);
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the ticket list
            jQuery(".mec-event-tickets-list").removeClass("loading");
        }
    });
}

function mec_check_tickets_availability(ticket_id, count)
{
    var total = jQuery("#mec_book_form_tickets_container").data("total-booking-limit");
    var max = jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max");
    
    var sum = 0;
    jQuery(".mec-book-ticket-limit").each(function()
    {
        sum += parseInt(jQuery(this).val(), 10);
    });
    
    if(total != "-1" && max > (total - (sum - count))) max = (total - (sum - count));
    
    if(parseInt(count) > parseInt(max)) jQuery("#mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val(max);
}

function mec_toggle_first_for_all()
{
    var status = jQuery("#mec_book_first_for_all").is(":checked") ? true : false;
    
    if(status)
    {
        jQuery(".mec-book-ticket-container:not(:first-child)").addClass("mec-util-hidden");
    }
    else
    {
        jQuery(".mec-book-ticket-container").removeClass("mec-util-hidden");
    }
}

function mec_book_form_submit()
{
    var step = jQuery("#mec_book_form input[name=step]").val();
    
    // Validate Checkboxes and Radio Buttons on Booking Form
    if(step == 2)
    {
        var valid = true;
        
        jQuery(".mec-book-ticket-container .mec-book-field-name.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][name]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-field-email.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][email]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-checkbox.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"][]\']").is(":checked"))
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-radio.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-agreement.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-tel.mec-reg-mandatory, .mec-book-ticket-container .mec-book-reg-field-email.mec-reg-mandatory, .mec-book-ticket-container .mec-book-reg-field-text.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            
            if(!jQuery("#mec_book_form input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-select.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            console.log(jQuery("#mec_book_form select[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val());
            if(!jQuery("#mec_book_form select[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        jQuery(".mec-book-ticket-container .mec-book-reg-field-textarea.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");
            
            if(!jQuery("#mec_book_form textarea[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
            }
            else jQuery(this).removeClass("mec-red-notification");
        });
        
        if(!valid) return false;
    }
    
    // Add loading Class to the button
    jQuery("#mec_book_form button[type=submit]").addClass("loading");
    jQuery("#mec_booking_message").removeClass("mec-success mec-error").hide();
    
    var data = jQuery("#mec_book_form").serialize();
    jQuery.ajax(
    {
        type: "GET",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form button[type=submit]").removeClass("loading");
            
            if(data.success)
            {
                jQuery("#mec_booking").html(data.output);

                jQuery("#mec_book_form").off("submit");
                jQuery("#mec_book_form").on("submit", function(event)
                {
                    event.preventDefault();
                    mec_book_form_submit();
                });
                
                // Show Invoice Link
                if(typeof data.data.invoice_link != "undefined" && data.data.invoice_link != "")
                {
                    jQuery("#mec_booking").append("<a class=\"mec-invoice-download\" href=\""+data.data.invoice_link+"\">'.esc_js(__('Download Invoice', 'mec')).'</a>");
                }
                
                // Redirect to thank you page
                if(typeof data.data.redirect_to != "undefined" && data.data.redirect_to != "")
                {
                    setTimeout(function(){window.location.href = data.data.redirect_to;}, 2000);
                }
            }
            else
            {
                jQuery("#mec_booking_message").addClass("mec-error").html(data.message).show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form button[type=submit]").removeClass("loading");
        }
    });
}

function mec_book_apply_coupon()
{
    // Add loading Class to the button
    jQuery("#mec_book_form_coupon button[type=submit]").addClass("loading");
    jQuery(".mec-book-form-coupon .mec-coupon-message").removeClass("mec-success mec-error").hide();
    
    var data = jQuery("#mec_book_form_coupon").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_coupon button[type=submit]").removeClass("loading");
            
            if(data.success)
            {
                // It converts to free booking because of applied coupon
                if(data.data.price_raw === 0)
                {
                    jQuery(".mec-book-form-gateways").hide();
                    jQuery("#mec_book_form_free_booking").show();
                }
                
                jQuery(".mec-book-form-coupon .mec-coupon-message").addClass("mec-success").html(data.message).show();
                
                jQuery(".mec-book-price-details .mec-book-price-detail-typediscount").remove();
                jQuery(".mec-book-price-details").append(data.data.price_details);
                
                jQuery(".mec-book-price-total").html(data.data.price);
                jQuery("#mec_do_transaction_paypal_express_form"+data.data.transaction_id+" input[name=amount]").val(data.data.price_raw);
            }
            else
            {
                jQuery(".mec-book-form-coupon .mec-coupon-message").addClass("mec-error").html(data.message).show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_coupon button[type=submit]").removeClass("loading");
        }
    });
}

function mec_book_free()
{
    // Add loading Class to the button
    jQuery("#mec_book_form_free_booking button[type=submit]").addClass("loading");
    jQuery("#mec_booking_message").removeClass("mec-success mec-error").hide();
    
    var data = jQuery("#mec_book_form_free_booking").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_free_booking button[type=submit]").removeClass("loading");
            
            if(data.success)
            {
                jQuery("#mec_booking").html(data.output);
                
                // Show Invoice Link
                if(typeof data.data.invoice_link != "undefined" && data.data.invoice_link != "")
                {
                    jQuery("#mec_booking").append("<a class=\"mec-invoice-download\" href=\""+data.data.invoice_link+"\">'.esc_js(__('Download Invoice', 'mec')).'</a>");
                }
                
                // Redirect to thank you page
                if(typeof data.data.redirect_to != "undefined" && data.data.redirect_to != "")
                {
                    setTimeout(function(){window.location.href = data.data.redirect_to;}, 2000);
                }
            }
            else
            {
                jQuery("#mec_booking_message").addClass("mec-error").html(data.message).show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_free_booking button[type=submit]").removeClass("loading");
        }
    });
}
</script>';

// Include javascript code into the footer
if($this->is_ajax()) echo $javascript;
else $factory->params('footer', $javascript);
?>
<div class="mec-booking" id="mec_booking">
    <?php
        $path = MEC::import('app.modules.booking.steps.tickets', true, true);
        include $path;
    ?>
</div>
<div id="mec_booking_message" class="mec-util-hidden"></div>