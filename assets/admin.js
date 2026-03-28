/**
 * Admin JavaScript for Vulnz Agent plugin.
 */
jQuery(document).ready(function ($) {
  $('#vulnz-agent-sync-now').on('click', function (e) {
    e.preventDefault();

    var $button = $(this);
    var labelDefault = $button.data('label');
    var labelSyncing = $button.data('syncing');

    $button.prop('disabled', true).text(labelSyncing);

    $.post(ajaxurl, {
      action: 'vulnz_agent_sync_now',
      nonce: vulnz_agent.nonce,
    })
      .done(function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert('Error: ' + response.data.message);
          $button.prop('disabled', false).text(labelDefault);
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
        $button.prop('disabled', false).text(labelDefault);
      });
  });
});
