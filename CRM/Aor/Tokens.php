<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 30/10/17
 * Time: 09:20
 */

class CRM_Aor_Tokens {

  public static function eventTokens() {
    return array(
      'event.type' => ts("Event Type"),
      'event.name' => ts("Event Name"),
      'event.membertickets' => ts("Number of member tickets"),
      'event.nonmembertickets' => ts("Number of non-member tickets"),
      'event.totaltickets' => ts("Total Number of tickets"),
      'event.membernetamount' => ts("Member net amount"),
      'event.membertaxamount' => ts("Member tax amount"),
      'event.membertotalamount' => ts("Member total amount"),
      'event.nonmembernetamount' => ts("Non Member net amount"),
      'event.nonmembertaxamount' => ts("Non Member tax amount"),
      'event.nonmembertotalamount' => ts("Non Member total amount"),
      'event.totalnetamount' => ts("Total Net Amount"),
      'event.totaltaxamount' => ts("Total Tax Amount"),
      'event.totalamount' => ts("Total Amount"),
    );
  }

  public static function memberTokens() {
    return array(
      'member.name' => ts('Membership Name'),
      'member.course_name' => ts('Membership Course Name'),
      'member.end_date' => ts('Membership End Date'),
      'member.start_date' => ts('Membership Start Date'),
      'member.join_date' => ts('Membership Join Date'),
      'member.qty' => ts("Membership Qty"),
      'member.totaltaxableamount' => ts("Membership Total Taxable Amount"),
      'member.totalnontaxableamount' => ts("Membership Total Non-Taxable Amount"),
      'member.totalnetamount' => ts("Membership Total Net Amount"),
      'member.totaltaxamount' => ts("Membership Total Tax Amount"),
      'member.totalamount' => ts("Membership Total Amount"),
      'member.lastnetamount' => ts("Membership (Last) Total Net Amount"),
      'member.lasttaxamount' => ts("Membership (Last) Total Tax Amount"),
      'member.lastamount' => ts("Membership (Last) Total Amount"),
    );
  }

  public static function cpdTokens() {
    return array(
      'cpd.course_name' => ts('CPD Course Name'),
      'cpd.end_date' => ts('CPD End Date'),
      'cpd.start_date' => ts('CPD Start Date'),
      'cpd.join_date' => ts('CPD Join Date'),
      'cpd.qty' => ts("CPD Qty"),
      'cpd.totaltaxableamount' => ts("CPD Total Taxable Amount"),
      'cpd.totalnontaxableamount' => ts("CPD Total Non-Taxable Amount"),
      'cpd.totalnetamount' => ts("CPD Total Net Amount"),
      'cpd.totaltaxamount' => ts("CPD Total Tax Amount"),
      'cpd.totalamount' => ts("CPD Total Amount"),
      'cpd.lastnetamount' => ts("CPD (Last) Total Net Amount"),
      'cpd.lasttaxamount' => ts("CPD (Last) Total Tax Amount"),
      'cpd.lastamount' => ts("CPD (Last) Total Amount"),
    );
  }

  public static function advertiserTokens() {
    return array(
      'advertiser.course_name' => ts('Advertiser Course Name'),
      'advertiser.end_date' => ts('Advertiser End Date'),
      'advertiser.start_date' => ts('Advertiser Start Date'),
      'advertiser.join_date' => ts('Advertiser Join Date'),
      'advertiser.qty' => ts("Advertiser Qty"),
      'advertiser.totaltaxableamount' => ts("Advertiser Total Taxable Amount"),
      'advertiser.totalnontaxableamount' => ts("Advertiser Total Non-Taxable Amount"),
      'advertiser.totalnetamount' => ts("Advertiser Total Net Amount"),
      'advertiser.totaltaxamount' => ts("Advertiser Total Tax Amount"),
      'advertiser.totalamount' => ts("Advertiser Total Amount"),
      'advertiser.lastnetamount' => ts("Advertiser (Last) Total Net Amount"),
      'advertiser.lasttaxamount' => ts("Advertiser (Last) Total Tax Amount"),
      'advertiser.lastamount' => ts("Advertiser (Last) Total Amount"),
    );
  }

  private static function eventTokenValues(&$values, $tokens, $contactId) {
    // Event tokens
    if (!empty($tokens['event'])) {
      $pid = CRM_Utils_Request::getValue('pid', $_REQUEST);
      if ($pid) {
        try {
          $participantRecord = civicrm_api3('Participant', 'getsingle', array(
            'id' => $pid,
          ));
        } catch (Exception $e) {
          return;
        }

        $participantPayments = civicrm_api3('ParticipantPayment', 'get', array(
          'participant_id' => $pid,
          'options' => array('limit' => 0),
        ));

        $member = $nonmember = $total = array();
        foreach ($participantPayments['values'] as $payment) {
          $lineItems = civicrm_api3('LineItem', 'get', array(
            'contribution_id' => $payment['contribution_id'],
            'options' => array('limit' => 0),
          ));
          $member = array(
            'qty' => NULL,
            'unit_price' => NULL,
            'line_total' => NULL,
            'tax_amount' => NULL,
          );
          $nonmember = array(
            'qty' => NULL,
            'unit_price' => NULL,
            'line_total' => NULL,
            'tax_amount' => NULL,
          );
          foreach ($lineItems['values'] as $item) {
            switch ($item['price_field_id']) {
              case '48': // "Member seminar price"
                $member['qty'] += (int) $item['qty'];
                $member['unit_price'] += (float) $item['unit_price'];
                $member['line_total'] += (float) $item['line_total'];
                $member['tax_amount'] += (float) $item['tax_amount'];
                break;
              /*case '17': // "Non member seminar price"
                $nonmember['qty'] += (int) $item['qty'];
                $nonmember['unit_price'] += (float) $item['unit_price'];
                $nonmember['line_total'] += (float) $item['line_total'];
                $nonmember['tax_amount'] += (float) $item['tax_amount'];
                break;*/
              case '56': // "Member recording price"
                $member['qty'] += (int) $item['qty'];
                $member['unit_price'] += (float) $item['unit_price'];
                $member['line_total'] += (float) $item['line_total'];
                $member['tax_amount'] += (float) $item['tax_amount'];
                break;
              case '58': // "Non member recording price"
                $nonmember['qty'] += (int) $item['qty'];
                $nonmember['unit_price'] += (float) $item['unit_price'];
                $nonmember['line_total'] += (float) $item['line_total'];
                $nonmember['tax_amount'] += (float) $item['tax_amount'];
                break;
            }
          }
          $total = array(
            'qty' => $member['qty'] + $nonmember['qty'],
            'unit_price' => $member['unit_price'] + $nonmember['unit_price'],
            'line_total' => $member['line_total'] + $nonmember['line_total'],
            'tax_amount' => $member['tax_amount'] + $nonmember['tax_amount'],
          );
        }

        $event = array(
          'event.type' => CRM_Utils_Array::value('event_type', $participantRecord),
          'event.name' => CRM_Utils_Array::value('event_title', $participantRecord),
          'event.membertickets' => $member['qty'],
          'event.nonmembertickets' => $nonmember['qty'],
          'event.membernetamount' => CRM_Utils_Money::format($member['line_total']),
          'event.membertaxamount' => CRM_Utils_Money::format($member['tax_amount']),
          'event.membertotalamount' => CRM_Utils_Money::format($member['line_total'] + $member['tax_amount']),
          'event.nonmembernetamount' => CRM_Utils_Money::format($nonmember['line_total']),
          'event.nonmembertaxamount' => CRM_Utils_Money::format($nonmember['tax_amount']),
          'event.nonmembertotalamount' => CRM_Utils_Money::format($nonmember['line_total'] + $nonmember['tax_amount']),
          'event.totaltickets' => $member['qty'] + $nonmember['qty'],
          'event.totalnetamount' => CRM_Utils_Money::format($total['line_total']),
          'event.totaltaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'event.totalamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
        );

        $values[$contactId] = empty($values[$contactId]) ? $event : array_merge($values[$contactId], $event);
      }
    }
  }

  private static function memberTokenValues(&$values, $tokens, $contactId) {
    if (!empty($tokens['member']) || !empty($tokens['cpd']) || !empty($tokens['advertiser'])) {
      if (!empty($values[$contactId]['membership_id'])) {
        $mid = $values[$contactId]['membership_id'];
      }
      else {
        $mid = CRM_Utils_Request::getValue('mid', $_REQUEST);
      }
      if (empty($mid)) {
        return;
      }

      try {
        $membershipRecord = civicrm_api3('Membership', 'getsingle', array('id' => $mid));
      } catch (Exception $e) {
        return;
      }

      // Get the membership fees from the membership priceset so we can populate "amount" tokens.
      $fees = CRM_Aor_Membership::getMembershipFeesFromPriceSet($membershipRecord);

      // Get the previous membership payments so we can populate "last*amount" tokens.
      $member = $total = $membership = array();
      $membershipPayments = civicrm_api3('MembershipPayment', 'get', array(
        'membership_id' => $mid,
        'options' => array('limit' => 0),
      ));

      foreach ($membershipPayments['values'] as $payment) {
        $lineItems = civicrm_api3('LineItem', 'get', array(
          'contribution_id' => $payment['contribution_id'],
          'options' => array('limit' => 0),
        ));
        $member = array(
          'qty' => NULL,
          'unit_price' => NULL,
          'line_total' => NULL,
          'tax_amount' => NULL,
        );
        foreach ($lineItems['values'] as $item) {
          $member['qty'] += (int) $item['qty'];
          $member['unit_price'] += (float) $item['unit_price'];
          $member['line_total'] += (float) $item['line_total'];
          $member['tax_amount'] += (float) $item['tax_amount'];
        }
        $total = array(
          'qty' => $member['qty'],
          'unit_price' => $member['unit_price'],
          'line_total' => $member['line_total'],
          'tax_amount' => $member['tax_amount'],
        );
      }

      if (CRM_Aor_Membership::isMembership($mid)) {
        $membership = array(
          'member.name' => CRM_Utils_Array::value('membership_name', $membershipRecord),
          'member.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'member.end_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('end_date', $membershipRecord)),
          'member.start_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('start_date', $membershipRecord)),
          'member.join_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('join_date', $membershipRecord)),
          'member.qty' => $member['qty'],
          'member.lastnetamount' => CRM_Utils_Money::format($total['line_total']),
          'member.lasttaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'member.lastamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
          'member.totaltaxableamount' => CRM_Utils_Money::format($fees['taxable_amount']),
          'member.totalnontaxableamount' => CRM_Utils_Money::format($fees['non_taxable_amount']),
          'member.totalnetamount' => CRM_Utils_Money::format($fees['net_amount']),
          'member.totaltaxamount' => CRM_Utils_Money::format($fees['tax_amount']),
          'member.totalamount' => CRM_Utils_Money::format($fees['total_amount']),
      );
      }
      elseif (CRM_Aor_Membership::isCpd($mid)) {
        $membership = array(
          'cpd.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'cpd.end_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('end_date', $membershipRecord)),
          'cpd.start_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('start_date', $membershipRecord)),
          'cpd.join_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('join_date', $membershipRecord)),
          'cpd.qty' => $member['qty'],
          'cpd.lastnetamount' => CRM_Utils_Money::format($total['line_total']),
          'cpd.lasttaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'cpd.lastamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
          'cpd.totaltaxableamount' => CRM_Utils_Money::format($fees['taxable_amount']),
          'cpd.totalnontaxableamount' => CRM_Utils_Money::format($fees['non_taxable_amount']),
          'cpd.totalnetamount' => CRM_Utils_Money::format($fees['net_amount']),
          'cpd.totaltaxamount' => CRM_Utils_Money::format($fees['tax_amount']),
          'cpd.totalamount' => CRM_Utils_Money::format($fees['total_amount']),
        );
      }
      elseif (CRM_Aor_Membership::isAdvertiser($mid)) {
        $membership = array(
          'advertiser.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'advertiser.end_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('end_date', $membershipRecord)),
          'advertiser.start_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('start_date', $membershipRecord)),
          'advertiser.join_date' => CRM_Utils_Date::customFormat(CRM_Utils_Array::value('join_date', $membershipRecord)),
          'advertiser.qty' => $member['qty'],
          'advertiser.lastnetamount' => CRM_Utils_Money::format($total['line_total']),
          'advertiser.lasttaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'advertiser.lastamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
          'advertiser.totaltaxableamount' => CRM_Utils_Money::format($fees['taxable_amount']),
          'advertiser.totalnontaxableamount' => CRM_Utils_Money::format($fees['non_taxable_amount']),
          'advertiser.totalnetamount' => CRM_Utils_Money::format($fees['net_amount']),
          'advertiser.totaltaxamount' => CRM_Utils_Money::format($fees['tax_amount']),
          'advertiser.totalamount' => CRM_Utils_Money::format($fees['total_amount']),
        );
      }

      $values[$contactId] = empty($values[$contactId]) ? $membership : array_merge($values[$contactId], $membership);
    }
  }

  public static function tokenValues(&$values, $cids, $tokens) {
    foreach ($cids as $key => $contactId) {
      self::eventTokenValues($values, $tokens, $contactId);
      self::memberTokenValues($values, $tokens, $contactId);
    }
  }
}