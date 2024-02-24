(function() {

  // marker icon selector
  jQuery('.marker_icons input[type=radio]').on('change', function(e) {
    jQuery('.marker_icons label').removeClass('checked');
    jQuery(this).parent('label').addClass('checked');
  });

})();
