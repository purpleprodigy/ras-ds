/**
 * RASDS JS
 *
 * @since 1.0.0
 *
 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
 */

var gfp_rasds_chart_js = {charts: []};
jQuery(document).trigger('gfp_rasds_object_declared');

/**
 * Bar Chart
 *
 * Taken from GFChart
 *
 * @since 1.0.0
 *
 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
 *
 * @param location
 * @param data
 * @constructor
 */
var GFP_RASDS_Bar = {

    /**
     * ID of the div where this chart will be rendered
     */
    location: '',

    data: '',

    options: '',

    init: function () {

        var obj = this;

        google.load('visualization', '1', {
            'packages': ['corechart'], 'callback': function () {
                obj.drawChart()
            }
        });

    },

    formatData: function () {
    },

    drawChart: function () {

        var data_table = new google.visualization.DataTable(this.data);

        var location = document.getElementById(this.location);

        var view = new google.visualization.DataView(data_table);

        if (true == this.options.segmented) {

            view.setColumns([0, 1,
                {
                    calc: function ( data_table, rowNum ) { return ( Math.round( data_table.getValue(rowNum, 1) * 100 ) ) + '%'},
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation"
                },
                2,
                {
                    calc: function ( data_table, rowNum ) { return ( Math.round( data_table.getValue(rowNum, 2) * 100 ) ) + '%'},
                    sourceColumn: 2,
                    type: "string",
                    role: "annotation"
                }]);

        }

        delete this.options.segmented;

        if ('horizontal' == this.options.bars) {

            var chart = new google.visualization.BarChart(location);

        }
        else {

            var chart = new google.visualization.ColumnChart(location);

        }

        chart.draw(view, this.options);

        jQuery('#' + this.location).show();

    }

};

(function ($) {

    /**
     * Draw chart
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param chart_type
     * @param id
     * @param data
     * @param options
     */
    function gfp_rasds_chart_draw(chart_type, id, data, options) {

        if ('object' == typeof( data )) {

            var chart_object = 'GFP_RASDS_' + chart_type;

            var chart_id = chart_type + '_chart_' + id;

            window[chart_id] = Object.create(this[chart_object], {
                'location': {value: 'gfp-rasds-' + chart_id},
                'data': {value: data},
                'options': {value: options}
            });

            window[chart_id].init();

        }

    }

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

        var link = this.href;

        $.post($('#ajaxurl').val(), {
            entry_id: entry_id,
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

        var entry_id = querystring_params['rasdsgventry'];

        link_obj.attr('href', '/rasds/dashboard/entry/' + entry_id + '?gvid=' + gvid);

    }

    /**
     * Add entry ID to Send to Second Email button link
     *
     * @since 1.0.0
     *
     * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
     *
     * @param link_obj
     */
//     function gfp_rasds_set_send_to_second_email_link(link_obj) {
//
//         var querystring_params = gfp_rasds_get_querystring_vars();
//
//         var entry_id = querystring_params['rasdsgventry'];
//
//         var link_href = link_obj.attr('href');
//
//         link_obj.attr('href', link_href + '?rasdsgventry=' + entry_id);
//
//     }
    function gfp_rasds_set_send_to_second_email_link(link_obj) {

        var querystring_params = gfp_rasds_get_querystring_vars();

        var entry_id = querystring_params['rasdsgventry'];

        link_obj.attr('href', '/rasds/dashboard/entry/' + 'rasdsgventry=' + entry_id);

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

        for (var i = 0; i < gfp_rasds_chart_js.charts.length; i++) {

            gfp_rasds_chart_draw(gfp_rasds_chart_js.charts[i].chart_type, gfp_rasds_chart_js.charts[i].id, gfp_rasds_chart_js.charts[i].data, gfp_rasds_chart_js.charts[i].options);

        }

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