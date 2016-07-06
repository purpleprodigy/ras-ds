/**
 * RASDS JS
 *
 * @since 1.0.0
 *
 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
 */

(function ($) {

    /**
     * Trigger notification when Email button is clicked
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param e
     * @returns {boolean}
     */
    function gfp_rasds_send_results(e) {

        var entry_id = $('#gravityview-entry-id').val();
        var comparison_id = $('#gravityview-comparison-id').val();

        var link = this.href;

        $.post($('#ajaxurl').val(), {
            entry_id: entry_id,
            comparison_id: comparison_id,
            action: 'rasds_send_pdf'
        }).done(function (response) {

            if (true === response.success) {

                window.location.href = link;
            }

        }).fail(function (response) {

            if (window.console && window.console.log) {

                console.log(response);

            }

        }).always();

        return false;
    }

    /**
     * Get values from URL query string
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @returns {Array}
     */
    function gfp_rasds_get_querystring_vars() {

        var vars = [], hash;

        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

        for (var i = 0; i < hashes.length; i++) {

            hash = hashes[i].split('=');

            vars.push(hash[0]);

            vars[hash[0]] = hash[1];

        }

        return vars;

    }

    /**
     * Set link for Back to Results button
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param link_obj
     */
    function gfp_rasds_set_back_to_results_link(link_obj) {

        var querystring_params = gfp_rasds_get_querystring_vars();
        var gvid = querystring_params['gvid'];
        var entry_id = 0;
        var comparison_id = 0;

        if ( 'rasdsgventry' in querystring_params ) {
            entry_id =  querystring_params['rasdsgventry'];
        }
        if ( 'entry_id' in querystring_params ) {
            entry_id =  querystring_params['entry_id'];
        }
        if ( 'rasdscompare' in querystring_params ) {
            comparison_id = querystring_params['rasdscompare'];
        }

        if ( comparison_id > 0 ) {
            link_obj.attr('href', '/rasds/dashboard/entry/' + entry_id + '?gvid=' + gvid + '&rasdscompare=' + comparison_id);
        } else {
            link_obj.attr('href', '/rasds/dashboard/entry/' + entry_id + '?gvid=' + gvid);
        }
    }

    /**
     * Set link for Send to second email address button
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param link_obj
     */
    function gfp_rasds_set_send_to_second_email_link(link_obj) {

        var querystring_params = gfp_rasds_get_querystring_vars();

        var gvid = querystring_params['gvid'];

        var entry_id = querystring_params['rasdsgventry'];

        if ( 'rasdscompare' in querystring_params ) {
            var comparison_id = querystring_params['rasdscompare'];
            link_obj.attr('href', '/rasds/send-results-to-a-second-email-address/?entry_id=' + entry_id + '&rasdscompare=' + comparison_id);
        } else {
            link_obj.attr('href', '/rasds/send-results-to-a-second-email-address/?entry_id=' + entry_id);
        }
    }

    /**
     * Validate selected comparison options
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param test1
     * @param test2
     * @returns {boolean}
     */
    function gfp_rasds_validate_compare_form(test1, test2) {

        var valid = false;

        if (0 < test1.length && 0 < test2.length && test1 !== test2) {

            valid = true;

        }

        return valid;
    }

    /**
     * Send user to entry comparison
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @returns {boolean}
     */
    function gfp_rasds_submit_compare_form() {

        var test1 = $('#rasds-compare-test1').val();

        var test2 = $('#rasds-compare-test2').val();

        var valid = gfp_rasds_validate_compare_form(test1, test2);

        if (valid) {

            window.location.href = '/rasds/dashboard/entry/' + test1 + '?gvid=352&rasdscompare=' + test2;

        }
        else {

            $('.error').text('Invalid choice');

        }

        return false;

    }

    $(document).ready(function () {

        var email_button = $('#rasds-email-entry');

        if (0 < email_button.length) {

            email_button.click(gfp_rasds_send_results);

        }

        var back_to_results_link = $('#rasds-back-to-results-link');

        if (0 < back_to_results_link.length) {

            gfp_rasds_set_back_to_results_link(back_to_results_link);

        }

        var send_to_second_email_link = $('#rasds-send-to-second-email-link');

        if (0 < send_to_second_email_link.length) {

            gfp_rasds_set_send_to_second_email_link(send_to_second_email_link);

        }

        var compare_form = $('#rasds-comparison-form');

        if (0 < compare_form.length) {

            compare_form.submit(gfp_rasds_submit_compare_form);

        }

    });

}(jQuery) );
