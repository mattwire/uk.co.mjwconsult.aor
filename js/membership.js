/**
 * Created by matthew on 31/05/17.
 */
function aor_filter_membership() {
    var vat_cb = [
        // Force selection of non-vatable amount
        CRM.$('div.non_vatable-row1 span input[type=checkbox]')
    ];
    vat_cb.forEach(membership_cb);
    var vat_cb_label = [
        CRM.$('div.non_vatable-section div.label label')
    ];
    vat_cb_label.forEach(membership_cb_label);

    // Hide the organisation dropdown as we only have one
    CRM.$('#membership_type_id_0').hide();	

    // If we can select a priceset, hide non-priceset membership selection
    if (CRM.$('#selectPriceSet').length){
        CRM.$('span#mem_type_id').hide();
        // Handler to set membership type - which ensures custom fields get saved on membership creation
        CRM.$('#Membership').submit(function(){
            var data;			
            CRM.$("#Membership input[type=radio]:checked").each(function(){
                if(data = CRM.$(this).data('priceFieldValues')){
                    if(data.hasOwnProperty(CRM.$(this).val()) && data[CRM.$(this).val()].hasOwnProperty('membership_type_id')){
                        CRM.$('#membership_type_id_1 option[value="' + data[CRM.$(this).val()].membership_type_id + '"]').prop('selected', true);
                        return false;
                    }
                }
            });
        });
    }
	
    CRM.$('a#hidePriceSet').hide();

    // Send receipt by default
    //var sendReceipt = CRM.$('input#send_receipt');
    //if (sendReceipt.prop('checked') === false) {
    //    sendReceipt.click();
    //}

    function membership_cb(vat_cb) {
        if (vat_cb.prop('checked') === false) {
            vat_cb.click();
        }
        vat_cb.hide();
    }

    function membership_cb_label(vat_cb_label) {
        vat_cb_label.hide();
    }
}
