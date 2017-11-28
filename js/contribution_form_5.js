CRM.$(function() {
  hideNonCheckedMembershipOptions();
  forceNonVatableAmount();
});

function forceNonVatableAmount() {
  CRM.$('#price_40_106').prop('checked', false);
  CRM.$('#price_40_106').trigger('click');
  CRM.$('div.label label:contains("Non Vatable")').hide();
  CRM.$('#price_40_106').hide();
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
