<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 30/10/17
 * Time: 09:33
 */

class CRM_Aor_Membership {
  const ADVERTISER_FINANCIAL_TYPES = array(6, 54);
  const MEMBERSHIP_FINANCIAL_TYPES = array(2, 42, 72, 81, 31, 30, 32);
  const MEMBERSHIP_PRICESETS = array(23, 24, 25, 26);
  const CPD_PRICESETS = array(15);
  const ADVERTISER_PRICESETS = array(31);

  /**
   * Returns the current Tax rate (VAT).  This could probably be improved to get from Civi via financial_type_id
   * @return float
   */
  public static function taxRate() {
    return 0.20;
  }

  /*
   * Returns array of financial type Ids used for membership. Use in api calls relating to membership
   * @return array
   */
  public static function getMembershipFinancialTypes() {
    // "Member Dues", Historical Member dues, membership inc vat, membership no vat, 6 month student, gap, 3 month student
    return array('IN' => self::MEMBERSHIP_FINANCIAL_TYPES);
  }

  public static function getMembershipPriceSets() {
    return self::MEMBERSHIP_PRICESETS;
  }

  public static function isMembership($mid) {
    $params = array(
      'id' => $mid,
      'api.membership_type.getsingle' => 1,
    );

    // Only get memberships with financial type "Member Dues"
    try {
      $membershipTypes = civicrm_api3('MembershipType', 'get', array(
        'financial_type_id' => self::getMembershipFinancialTypes(),
        'options' => array('limit' => 0),
      ));
    }
    catch (Exception $e) {
      // One of the financial_types is not defined, probably on a dev environment
      Civi::log()->debug('_aor_is_membership: ' . $e->getMessage());
      return TRUE; // Assume all membership types are valid
    }
    $types = array();
    foreach ($membershipTypes['values'] as $typeId => $val) {
      $types[] = $val['name'];
    }
    $params['membership_type_id'] = array('IN' => $types);

    try {
      $membership = civicrm_api3('Membership', 'getsingle', $params);
    }
    catch (Exception $e) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Get the latest membership for contact id.
   * @param $cid
   *
   * @return array|null
   */
  public static function getLatestMembership($cid) {
    $params = array(
      'contact_id' => $cid,
      'sequential' => 1,
      'api.membership_type.getsingle' => 1,
      'options' => array('limit' => 1, 'sort' => 'end_date DESC'),
    );

    // Only get memberships with financial type "Member Dues"
    try {
      $membershipTypes = civicrm_api3('MembershipType', 'get', array(
        'financial_type_id' => self::getMembershipFinancialTypes(),
        'options' => array('limit' => 0),
      ));
    }
    catch (Exception $e) {
      Civi::log()->info('uk.co.mjwconsult.aor: Invalid financial type ' . $e->getMessage());
      return NULL;
    }
    $types = array();
    foreach ($membershipTypes['values'] as $typeId => $val) {
      $types[] = $val['name'];
    }
    $params['membership_type_id'] = array('IN' => $types);

    try {
      $membership = civicrm_api3('Membership', 'getsingle', $params);
    }
    catch (Exception $e) {
      Civi::log()->info('uk.co.mjwconsult.aor: No membership found ' . $e->getMessage());
      return NULL;
    }
    return $membership;
  }

  public static function getCpdPriceSets() {
    return self::CPD_PRICESETS;
  }

  public static function isCpd($mid) {
    $params = array(
      'id' => $mid,
      'api.membership_type.getsingle' => 1,
    );

    // Only get memberships with type "CPD"
    $params['membership_type_id'] = "CPD";

    try {
      $membership = civicrm_api3('Membership', 'getsingle', $params);
    }
    catch (Exception $e) {
      return FALSE;
    }
    return TRUE;
  }

  public static function getAdvertiserFinancialTypes() {
    return array('IN' => self::ADVERTISER_FINANCIAL_TYPES); // "Advertising", Historical Advertising
  }

  public static function getAdvertiserPriceSets() {
    return self::ADVERTISER_PRICESETS; // "Advertising", Historical Advertising
  }

  public static function isAdvertiser($mid) {
    $params = array(
      'id' => $mid,
      'api.membership_type.getsingle' => 1,
    );

    // Only get memberships with financial type "Member Dues"
    $membershipTypes = civicrm_api3('MembershipType', 'get', array(
      'financial_type_id' => self::getAdvertiserFinancialTypes(),
      'options' => array('limit' => 0),
    ));
    $types = array();
    foreach ($membershipTypes['values'] as $typeId => $val) {
      $types[] = $val['name'];
    }
    $params['membership_type_id'] = array('IN' => $types);

    try {
      $membership = civicrm_api3('Membership', 'getsingle', $params);
    }
    catch (Exception $e) {
      return FALSE;
    }
    return TRUE;
  }

  public static function getMembershipFeesFromPriceSet($membershipRecord) {
    // Final values, calculated from membership_fee_taxable_amount and membership_fee_non_taxable_amount
    $values['taxable_amount'] = 0;
    $values['non_taxable_amount'] = 0;
    $values['net_amount'] = 0;
    $values['tax_amount'] = 0;
    $values['total_amount'] = 0;

    // Set below, if we find a membership fee
    $values['membership_fee_taxable_amount'] = 0;
    // Set below, if we find a non taxable amount
    $values['membership_fee_non_taxable_amount'] = 0;

    if (!isset($membershipRecord)) {
      return $values;
    }

    if (self::isMembership($membershipRecord['id'])) {
      $priceSets = CRM_Aor_Membership::getMembershipPriceSets();
    }
    elseif (self::isCpd($membershipRecord['id'])) {
      $priceSets = CRM_Aor_Membership::getCpdPriceSets();
    }
    elseif (self::isAdvertiser($membershipRecord['id'])) {
      $priceSets = CRM_Aor_Membership::getAdvertiserPriceSets();
    }
    else {
      return $values;
    }

    // Look through each priceset for a membership fee / non-taxable amount
    foreach ($priceSets as $priceset) {
      //if ($values['membership_fee_taxable_amount'] && $values['membership_fee_non_taxable_amount']) {
      if ($values['membership_fee_taxable_amount']) {
        // Stop looping if we found what we needed.
        break;
      }
      else {
        // Reset both, as they must be populated from the same priceset
        $values['membership_fee_taxable_amount'] = 0;
        $values['membership_fee_non_taxable_amount'] = 0;
      }

      // Get price fields
      $priceFields = civicrm_api3('PriceField', 'get', array(
        'price_set_id' => $priceset,
        'options' => array('limit' => 0),
      ));
      if (empty($priceFields['count'])) {
        continue;
      }
      foreach ($priceFields['values'] as $priceFieldId => $pfValues) {
        // Get pricefieldvalues for each pricefield
        $priceFieldValues = civicrm_api3('PriceFieldValue', 'get', array(
          'return' => array("membership_type_id", "amount"),
          'price_field_id' => $priceFieldId,
          'options' => array('limit' => 0),
          'is_active' => 1,
        ));
        if (empty($priceFieldValues['count'])) {
          continue;
        }
        // Now look through each pricefieldvalue to see if it has a matching membership type,
        //  if it does, use the amount.
        foreach ($priceFieldValues['values'] as $priceFieldValueId => $pfvValues) {
          if ($values['membership_fee_taxable_amount'] && $values['membership_fee_non_taxable_amount']) {
            // Stop looping if we found what we needed.
            break;
          }
          if (!empty($pfvValues['membership_type_id'])) {
            // If a membership type is found, we assume that one of the membership types should match.
            if ($pfvValues['membership_type_id'] === $membershipRecord['membership_type_id']) {
              $values['membership_fee_taxable_amount'] = $pfvValues['amount'];
            }
          }
          else {
            // If a pricefieldvalue is found without a membership type we assume it's a non-vatable amount and use it
            $values['membership_fee_non_taxable_amount'] = $pfvValues['amount'];
          }
        }
      }
    }
    // Final values, calculated from membership_fee_taxable_amount and membership_fee_non_taxable_amount
    $values['taxable_amount'] = $values['membership_fee_taxable_amount'];
    $values['non_taxable_amount'] = $values['membership_fee_non_taxable_amount'];
    $values['net_amount'] = $values['taxable_amount'] + $values['non_taxable_amount'];
    $values['tax_amount'] = $values['membership_fee_taxable_amount'] * self::taxRate();
    $values['total_amount'] = $values['taxable_amount'] + $values['tax_amount'] + $values['non_taxable_amount'];
    return $values;
  }

  /**
   * For each member Id, get all the contributions and set the "thankyou_sent" field to Datetime.now for those contributions
   * @param $memberIds: values are civi membership IDs
   *
   */
  public static function setContributionThankyouDate($memberIds) {
    $nowDate = date('YmdHis');
    foreach ($memberIds as $membershipId) {
      // Get contributions for membership
      $contributionResult = civicrm_api3('MembershipPayment', 'get', array(
        'membership_id.id' => $membershipId,
        'options' => array('limit' => 0),
      ));
      if (empty($contributionResult['count'])) {
        continue;
      }
      // Set thankyou_sent to "now" for associated contributions
      foreach ($contributionResult['values'] as $key => $values) {
        if (empty($values['contribution_id'])) {
          continue;
        }
        // Update receipt/thankyou dates
        $contributionParams = array(
          'id' => $values['contribution_id'],
          'thankyou_date' => $nowDate,
        );
        $result = civicrm_api3('Contribution', 'create', $contributionParams);
      }
    }
  }
}