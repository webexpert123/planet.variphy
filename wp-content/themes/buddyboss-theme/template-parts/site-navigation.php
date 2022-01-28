<nav id="site-navigation" class="main-navigation" data-menu-space="120">
    <div id="primary-navbar">
	    <?php
	    wp_nav_menu( array(
			    'theme_location' => 'header-menu',
			    'menu_id'        => 'primary-menu',
			    'container'      => false,
			    'fallback_cb'    => '',
			    'walker'         => new BuddyBoss_SubMenuWrap(),
			    'menu_class'     => 'primary-menu bb-primary-overflow',
		    )
	    );
    	?>
        <div id="navbar-collapse">
            <a class="more-button" href="#"><i class="bb-icon-menu-dots-h"></i></a>
            <div class="sub-menu">
                <div class="wrapper">
                    <ul id="navbar-extend" class="sub-menu-inner"></ul>
                </div>
            </div>
        </div>
    </div>
</nav>
