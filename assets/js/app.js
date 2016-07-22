$(document).ready(function () {
    var $cost_mode = $('#cost').data('cost-mode');

    $('input:radio[name="Cost[cost_rules]"]').filter('[value='+ $cost_mode +']').click();
});
