(function( $ ) {

    $('.cmb2-id--gamipress-leaderboards-metrics input').on('change', function() {

        // Makes columns sortable

        $('.cmb2-id--gamipress-leaderboards-columns .cmb2-list').sortable({
            handle: 'label',
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
        });

        // Columns visibility

        var target = $('.cmb2-id--gamipress-leaderboards-columns input[value="' +  $(this).val() + '"]');

        if( ! target.length ) {
            return;
        }

        if( $(this).prop('checked') ) {
            //target.prop('checked', true);
            target.parent().show();
        } else {
            //target.prop('checked', false);
            target.parent().hide();
        }
    });

    $('.cmb2-id--gamipress-leaderboards-metrics input').each(function() {
        $(this).trigger('change');
    });

    // Period start and end dates visibility

    $('#_gamipress_leaderboards_period').on('change', function() {
        var target = $('.cmb2-id--gamipress-leaderboards-period-start-date, .cmb2-id--gamipress-leaderboards-period-end-date');

        if( $(this).val() === 'custom' ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    if( $('#_gamipress_leaderboards_period').val() !== 'custom' ) {
        $('.cmb2-id--gamipress-leaderboards-period-start-date, .cmb2-id--gamipress-leaderboards-period-end-date').hide();
    }

    // Avatar size visibility

    $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="avatar"]').on('change', function() {
        var target = $('.cmb2-id--gamipress-leaderboards-avatar-size');

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    // Merger avatar and name

    $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="avatar"], .cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="display_name"]').on('change', function() {
        var target = $('.cmb2-id--gamipress-leaderboards-merge-avatar-and-name');

        if( $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="avatar"]').prop('checked')
            && $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="display_name"]').prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="avatar"]').trigger('change');
    $('.cmb2-id--gamipress-leaderboards-columns input[type="checkbox"][value="display_name"]').trigger('change');

    // Cache duration visibility

    $('#_gamipress_leaderboards_cache').on('change', function() {
        var target = $('.cmb2-id--gamipress-leaderboards-cache-duration');

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    $('#_gamipress_leaderboards_cache').trigger('change');

    // Clear cache functionality

    $('#_gamipress_leaderboards_clear_cache').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop('disabled', true);

        // Show the spinner
        $this.parent().append('<span id="gamipress-leaderboards-clear-cache-response"><span class="spinner is-active" style="float: none;"></span></span>');

        $.post(
            ajaxurl,
            {
                action: 'gamipress_leaderboards_clear_cache',
                nonce: gamipress_leaderboards_admin.nonce,
                leaderboard_id: $('#post_ID').val()
            },
            function( response ) {

                // Remove the spinner
                $this.parent().find('.spinner').remove();

                if( response.success === false ) {
                    $('#gamipress-leaderboards-clear-cache-response').css({color:'#a00'});
                } else {
                    // TODO: Localization here
                    $('#gamiress-leaderboards-cache-information').html( 'No cache stored.' );

                    $('#gamipress-leaderboards-clear-cache-response').css({color:''});
                }

                $('#gamipress-leaderboards-clear-cache-response').html( response.data );

                $this.prop('disabled', false);
            }
        );
    });

    // Leaderboard slug setting

    $('#gamipress_leaderboards_slug').on( 'keyup', function() {
        var field = $(this);
        var slug = $(this).val();
        var preview = $(this).next('.cmb2-metabox-description').find('.gamipress-leaderboards-slug');

        if( preview.length )
            preview.text(slug);

        // Delete any existing version of this warning
        $('#slug-warning').remove();

        // Throw a warning on Points/Achievement Type editor if slig is > 20 characters
        if ( slug.length > 20 ) {
            // Set input to look like danger
            field.css({'background':'#faa', 'color':'#a00', 'border-color':'#a55' });

            // Output a custom warning
            // TODO: Localization here
            field.parent().append('<span id="slug-warning" class="cmb2-metabox-description" style="color: #a00;">Leadeboard\'s slug supports a maximum of 20 characters.</span>');
        } else {
            // Restore the input style
            field.css({'background':'', 'color':'', 'border-color':''});
        }
    });

    $('#gamipress_leaderboards_slug').keyup();

})( jQuery );