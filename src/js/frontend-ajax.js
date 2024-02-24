document.addEventListener('DOMContentLoaded', function(e) {
  
  // Event: "Add Location"-Form send
  jQuery('#oum_add_location').submit(function(event) {

    jQuery('#oum_submit_btn').addClass('oum-loading');
    
    event.preventDefault();
    let formData = new FormData(this);

    formData.append('action','oum_add_location_from_frontend');

    jQuery.ajax({
      type: 'POST',
      url: oum_ajax.ajaxurl,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      success: function (response, textStatus, XMLHttpRequest) {
        jQuery('#oum_submit_btn').removeClass('oum-loading');

        if(response.success == false) {
          oumShowError(response.data);
        }
        if(response.success == true) {
          jQuery('#oum_add_location').trigger('reset');
          
          if(oum_action_after_submit == 'refresh') {
            oumRefresh();
          }else if(oum_action_after_submit == 'redirect' && thankyou_redirect !== '') {
            oumRedirect(thankyou_redirect);
          }else{
            oumShowThankYou();
          }
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) { 
        console.log(errorThrown);
      }
    });
  });

  function oumShowThankYou() {
    jQuery('#oum_add_location').hide();
    jQuery('#oum_add_location_error').hide();
    jQuery('#oum_add_location_thankyou').show();
  }

  function oumRefresh() {
    window.location.reload();
  }

  function oumRedirect(url) {
    window.location.href = url;
  }

  function oumShowError(errors) {
    const errorWrapEl = jQuery('#oum_add_location_error');
    errorWrapEl.html('');
    errors.forEach(error => {
      errorWrapEl.append(error.message + '<br>');
    });
    errorWrapEl.show();
  }
});