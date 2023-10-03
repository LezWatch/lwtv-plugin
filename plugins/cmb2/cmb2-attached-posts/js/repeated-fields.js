if (window.CMBAP) {
	jQuery('.cmb-repeatable-group').on('cmb2_add_row', function(event, newRow) {
	  var app = window.CMBAP;
  
	  // draggables
	  app.cache();
	  app.makeDraggable();
	  app.makeDroppable();
  
	  // reset
	  jQuery(newRow).find('.attached-posts-wrap').each(function() {
		var $wrap = jQuery(this);
		$wrap.find('.attached-posts-ids').val('');
  
		$wrap.find('.retrieved li').each(function() {
			jQuery(this).removeClass('added');
		});
		$wrap.find('.attached li').each(function() {
			jQuery(this).remove();
		});
  
		app.resetAttachedListItems($wrap);
		app.updateReadOnly($wrap);
		app.updateRemaining($wrap);
	  });
  
	  app.cache();
	});
  }
