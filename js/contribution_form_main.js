CRM.$(function() {
  forceNonVatableAmount();
});

function forceNonVatableAmount() {
  CRM.$('#price_40_106').prop('checked', true);
  CRM.$('#price_40_106').hide();
  CRM.$('div.label label:contains("Non Vatable")').hide();
}