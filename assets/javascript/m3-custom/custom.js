//Fix flyout menu parent submenu link unclickable issue
jQuery('.mega-menu li.is-dropdown-submenu-parent > a').click(function(){
	jQuery(this).unbind('click');
});