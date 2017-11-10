{if $form.thankyou_update}
  <div class="crm-member-thankyou-extra">
    <div class="content">
      {$form.thankyou_update.html}
      <label for="thankyou_update">{$form.thankyou_update.label}</label>
    </div>
    <div class="clear">&nbsp;</div>
  </div>
{/if}

{literal}
  {literal}
<script type="text/javascript">
  CRM.$(function($) {
    $('.crm-member-thankyou-extra').insertBefore('.crm-submit-buttons:last');
  });

</script>
{/literal}
