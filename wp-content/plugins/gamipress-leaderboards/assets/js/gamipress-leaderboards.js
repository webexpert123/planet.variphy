(function( $ ) {

    if( ! gamipress_leaderboards.disable_datatables ) {

        // DataTables
        $('.gamipress-leaderboard-table').each(function() {

            var $this = $(this);

            $this.DataTable( {
                filter:   $this.hasClass('gamipress-leaderboard-search-enabled'),
                ordering: $this.hasClass('gamipress-leaderboard-sort-enabled'),
                paging:   false,
                info:     false,
                language: gamipress_leaderboards.language,
                columnDefs: [
                    { targets: 'no-sortable', orderable: false }
                ]
            } );

        });

    }

    // Infinite scrolling pagination
    $('body').on('visibleOnScroll', '.gamipress-leaderboards-infinite-scrolling', function (e) {

        var container = $(this);
        var spinner = container.find('.gamipress-spinner');

        // Check if is loading
        if( container.hasClass('gamipress-leaderboards-loading') ) {
            return false;
        }

        // Class to check meet that is loading
        container.addClass('gamipress-leaderboards-loading');

        // Show the spinner
        spinner.show();

        // Setup vars
        var leaderboard_id = parseInt( container.data( 'leaderboard-id' ) );
        var page = parseInt( container.data( 'page' ) );

        // Increment the current page
        page++;

        // Update the current page attribute
        container.data( 'page', page );

        $.ajax({
            url: gamipress_leaderboards.ajaxurl,
            data: {
                action: 'gamipress_leaderboards_get_leaderboard_results',
                leaderboard_id: leaderboard_id,
                page: page,
            },
            success: function( response ) {

                // Hide the spinner
                spinner.hide();

                var items = response.data.items;
                var items_keys = Object.keys( items );

                if( items_keys.length ) {

                    var parent = container.closest('.gamipress-leaderboard');

                    if( ! parent.length ) {
                        parent = container.closest('.single-gamipress-leaderboard');
                    }

                    var table = parent.find('.gamipress-leaderboard-table');

                    // If datatables enabled, turn table to a DataTable object
                    if( ! gamipress_leaderboards.disable_datatables ) {
                        table = table.DataTable();
                    }

                    items_keys.forEach( function ( i ) {
                        var row = items[i];

                        var tr = $('<tr>').append( Object.values( row ).join('') );

                        if( ! gamipress_leaderboards.disable_datatables ) {
                            // DataTables
                            table.row.add( tr ).draw( false );
                        } else {
                            // HTML table
                            table.append( tr );
                        }
                    } );


                    // Remove the loading class for the next page
                    container.removeClass('gamipress-leaderboards-loading');

                    // Check again if should load the infinite scrolling
                    gamipress_leaderboards_check_infinite_scrolling();

                } else {
                    // If not has any items, remove this container to
                    container.remove();
                }

                if( response.data.more_items === false ) {
                    // If not has any items, remove this container to
                    container.remove();
                }

            },
            error: function( response ) {
                // Hide the spinner
                spinner.hide();
            }
        });

    });

    // Checks if any leaderboard infinite scrolling element is visible
    function gamipress_leaderboards_check_infinite_scrolling() {

        $('.gamipress-leaderboards-infinite-scrolling:not(.gamipress-leaderboards-loading)').each(function() {
            if( ( $(window).scrollTop() + $(window).height() ) >= ( $(this).offset().top + $(this).height() ) ) {
                $('body').trigger( $.Event('visibleOnScroll', { target: this }) );
            }
        });

    }

    // On window scroll, check infinite scrolling
    $(window).on('scroll touchstart touchend', function (e) {
        gamipress_leaderboards_check_infinite_scrolling();
    });

    // Trigger this check on get the window loaded
    gamipress_leaderboards_check_infinite_scrolling();

    var toggleable_selector = '.gamipress-leaderboard-table-responsive.gamipress-leaderboard-table-responsive-toggleable:not(.gamipress-leaderboard-table-force-responsive-toggleable)';

    // Toggleable columns for responsive leaderboards
    $('body').on('click', toggleable_selector + ' tr td.column-position, '
        + toggleable_selector + ' tr td.column-avatar, '
        + toggleable_selector + ' tr td.column-display_name', function (e) {

        //  Check if is mobile or table screen
        if( window.innerWidth > 1024 ) {
            return;
        }

        gamipress_leaderbaords_toggle_columns( $(this).closest('tr'), e );

    });

    // Toggleable columns for responsive forced leaderboards
    $('body').on('click', '.gamipress-leaderboard-table-force-responsive-toggleable tr td.column-position, '
        + '.gamipress-leaderboard-table-force-responsive-toggleable tr td.column-avatar, '
        + '.gamipress-leaderboard-table-force-responsive-toggleable tr td.column-display_name', function (e) {

        gamipress_leaderbaords_toggle_columns( $(this).closest('tr'), e );

    });

    function gamipress_leaderbaords_toggle_columns( row, e ) {

        // Prevent to toggle when clicking an anchor
        if( $(e.target)[0].nodeName === 'A' || ( $(e.target).parent() && $(e.target).parent()[0].nodeName === 'A' ) ) {
            return;
        }

        var columns = row.find('td:not(.column-position):not(.column-avatar):not(.column-display_name)');

        columns.each(function() {
            if( $(this).is(':visible') ) {
                $(this).slideUp( 'fast' );
            } else {
                $(this).slideDown( 'fast' );
            }
        });
    }

})( jQuery );