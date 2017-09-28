CRM.$(function() {
  forceNonVatableAmount();
  hideNonCheckedMembershipOptions();
});

function forceNonVatableAmount() {
  CRM.$('#price_40_106').prop('checked', true);
  CRM.$('#price_40_106').hide();
  CRM.$('div.label label:contains("Non Vatable")').hide();
}

function hideNonCheckedMembershipOptions() {
  CRM.$('div.price-set-row span.price-set-option-content input:radio').each(function() {
    if(CRM.$(this).is(':checked')) {
      CRM.$(this).hide();
    }
    else {
      CRM.$(this).parent().hide();
    }
  });
}