{$form.participant_id.html}
{$form.membership_id.html}

<script type="text/javascript">

  {literal}
  CRM.$(function($) {
    var pidElement = CRM.$('#participant_id');
    var midElement = CRM.$('#membership_id');
    pidElement.detach();
    pidElement.insertAfter('#document_type');
    midElement.detach();
    midElement.insertAfter('#document_type');
  });
  {/literal}
</script>
