 
$(document).ready(function() {
	$('body').click(
		function(e) {
		if(e.target.id!="menu-toggle")
		{ 
			if($(e.target).parent()[0].id!="menu-toggle")
			{ 
				$("#wrapper").removeClass('toggled')
			}
		}
		 
		if($(e.target).parent()[0].id!="menu-toggle")
		{ 
			if(e.target.id!="menu-toggle")
			{ 
				$("#wrapper").removeClass('toggled')
			}
		}
	})
});