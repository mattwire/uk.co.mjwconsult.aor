/**
 * Created by matthew on 31/05/17.
 */
function aor_filter_membership() {
    /* FIXME: This needs changing to lowercase for civi 4.7 */
    var vat_cb = [
        CRM.$('div.Membership_Fee_Ex_VAT_-row1 span input[type=checkbox]'),
        CRM.$('div.VAT_on_Membership-row1 span input[type=checkbox]')
    ];
    vat_cb.forEach(membership_cb);
    var vat_cb_label = [
        CRM.$('div.Membership_Fee_Ex_VAT_-section div.label label'),
        CRM.$('div.VAT_on_Membership-section div.label label')
    ];
    vat_cb_label.forEach(membership_cb_label);

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
