jQuery(function () {
    jQuery('#dpd_bukvycja_plugin_color_picker').wpColorPicker();
});

function allArcCheck() {
    document.getElementById('allarc_enable').checked = (
            document.getElementById('arctag_enable').checked &&
            document.getElementById('arccat_enable').checked &&
            document.getElementById('arcdate_enable').checked &&
            document.getElementById('arcauth_enable').checked);
};

function checkAllArcElements(val) {
    if (val) {
        document.getElementById('arctag_enable').checked = true;
        document.getElementById('arccat_enable').checked = true;
        document.getElementById('arcdate_enable').checked = true;
        document.getElementById('arcauth_enable').checked = true;
    }
    document.getElementById('allarc_enable').checked = (
            document.getElementById('arctag_enable').checked &&
            document.getElementById('arccat_enable').checked &&
            document.getElementById('arcdate_enable').checked &&
            document.getElementById('arcauth_enable').checked);
};