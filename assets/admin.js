/**
 * Admin JavaScript for Vulnz Agent plugin.
 */
console.log('Vulnz Agent Admin JS Loaded');

jQuery(document).ready(function ($) {
  $('#vulnz-agent-sync-now').on('click', function (e) {
    e.preventDefault();

    var $button = $(this);
    $button.prop('disabled', true);

    $.post(ajaxurl, {
      action: 'vulnz_agent_sync_now',
      nonce: vulnz_agent.nonce,
    })
      .done(function (response) {
        if (response.success) {
          alert('Sync complete!');
          location.reload();
        } else {
          alert('Error: ' + response.data.message);
        }
      })
      .fail(function (jqXHR) {
        var message = 'An unknown error occurred.';
        if (
          jqXHR.responseJSON &&
          jqXHR.responseJSON.data &&
          jqXHR.responseJSON.data.message
        ) {
          message = jqXHR.responseJSON.data.message;
        }
        alert('Error: ' + message);
      })
      .always(function () {
        $button.prop('disabled', false);
      });
  });
});
