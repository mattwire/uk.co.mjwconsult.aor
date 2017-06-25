{literal}
<script type="text/javascript">
    CRM.$(function() {
        aor_filter_membership();
    });

    CRM.$('.crm-membership-form-block-membership_type_id td select#price_set_id').change( function() {
        aor_filter_membership();
    });
</script>
{/literal}