(function( $ ) {

    var prefix = '_gamipress_leaderboards_include_exclude_users_';

    // On change switch, display fields
    $( '#' + prefix + 'include, #' + prefix + 'exclude' ).on('change', function(e) {
        var target = $('#' + $(this).attr('id') + '_roles, #' + $(this).attr('id') + '_users' ).closest('.cmb-row');

        console.log($(this).prop('checked'));

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    // Trigger initial change event
    $('#' + prefix + 'include, #' + prefix + 'exclude').trigger('change');

    // Include/Exclude roles
    $( '#' + prefix + 'include_roles, #' + prefix + 'exclude_roles' ).select2({
        theme: 'default gamipress-select2',
        placeholder: 'Select Roles',
        allowClear: true,
        multiple: true
    });

    // Include/Exclude users
    $( '#' + prefix + 'include_users, #' + prefix + 'exclude_users' ).select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_users',
                    nonce: gamipress_leaderboards_include_exclude_users_admin.nonce
                };
            },
            processResults: gamipress_select2_users_process_results
        },
        escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
        templateResult: gamipress_select2_users_template_result,
        theme: 'default gamipress-select2',
        placeholder: 'Select Users',
        allowClear: true,
        closeOnSelect: false,
        multiple: true
    });

})( jQuery );