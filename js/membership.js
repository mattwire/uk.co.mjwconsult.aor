/**
 * Created by matthew on 31/05/17.
 */
function aor_filter_membership() {
    /* FIXME: This needs changing to lowercase for civi 4.7 */
    var vat_cb = [
        // Force selection of non-vatable amount
        CRM.$('div.non_vatable-row1 span input[type=checkbox]')
    ];
    vat_cb.forEach(membership_cb);
    var vat_cb_label = [
        CRM.$('div.non_vatable-section div.label label')
    ];
    vat_cb_label.forEach(membership_cb_label);

    // Send receipt by default
    var sendReceipt = CRM.$('input#send_receipt');
    if (sendReceipt.prop('checked') === false) {
        sendReceipt.click();
    }

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
