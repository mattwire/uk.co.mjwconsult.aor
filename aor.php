<?php

require_once 'aor.civix.php';

/**
 * Returns array of financial type Ids used for membership. Use in api calls relating to membership
 * @return array
 */
function _aor_getMembershipFinancialTypes() {
  // "Member Dues", Historical Member dues, membership inc vat, membership no vat, 6 month student, gap, 3 month student
  return array('IN' => array(2, 42, 72, 81, 31, 30, 32));
}

function _aor_getAdvertiserFinancialTypes() {
  return array('IN' => array(6, 54)); // "Advertising", Historical Advertising
}

/**
 * Membership Number custom field for memberships (this is copied from contact external_identifier for the latest current membership only)
 * @return string
 */
function _aor_getMembershipNoCustomField() {
  return 'custom_35';
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function aor_civicrm_config(&$config) {
  _aor_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function aor_civicrm_xmlMenu(&$files) {
  _aor_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function aor_civicrm_install() {
  _aor_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function aor_civicrm_postInstall() {
  _aor_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function aor_civicrm_uninstall() {
  _aor_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function aor_civicrm_enable() {
  _aor_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function aor_civicrm_disable() {
  _aor_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function aor_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _aor_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function aor_civicrm_managed(&$entities) {
  _aor_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function aor_civicrm_caseTypes(&$caseTypes) {
  _aor_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function aor_civicrm_angularModules(&$angularModules) {
  _aor_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function aor_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _aor_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Alter the display of CPD Tutor custom group tab
 * @param $content
 * @param $context
 * @param $tplName
 * @param $object
 */
function aor_civicrm_alterContent(  &$content, $context, $tplName, &$object ) {
  if ($object instanceof CRM_Member_Page_Tab) {
    $template = CRM_Core_Smarty::singleton();
    $content .= $template->fetch('CRM/aor/membership.js.tpl');
  }

  if ($object instanceof CRM_Contact_Page_View_CustomData) {
    $customGroup = cpdtutor_get_custom_group();
    if ($object->_groupId == $customGroup['id']) {
      // We are viewing CPD Tutor information
      // Get the start and end of the table used for display
      $tableStartIndex = strpos($content, '<table id="records"');
      if ($tableStartIndex === FALSE) {
        // No entries
        return;
      }
      $tableEndIndex = strpos($content, '</tr></table>') + 13;
      $tableContent = substr($content, $tableStartIndex, $tableEndIndex - $tableStartIndex);

      // Now get all Ids
      $index=0;
      $membershipIds = array();
      $courseIdField = cpdcourseid_get_custom_field();

      while ($index !== FALSE) {
        // Get the api key
        $apiStart = strpos($tableContent, '&quot;key&quot;:&quot;') + 22;
        $apiEnd = strpos($tableContent, '&quot;', $apiStart);
        $apiStr = substr($tableContent, $apiStart, $apiEnd - $apiStart);

        // Get the record id (from "crmf-custom_103_4 ")
        $needle = 'crmf-custom_' . $courseIdField['id'];
        $recordIdStart = strpos($tableContent, $needle);
        if ($recordIdStart === FALSE) {
          break;
        }
        else {
          $recordIdStart += strlen($needle) + 1;
        }
        $tableContent = substr($tableContent, $recordIdStart);
        $recordIdEnd = strpos($tableContent, ' ');
        if ($recordIdEnd === FALSE) {
          break;
        }
        $recordId = substr($tableContent, 0, $recordIdEnd);

        $index = strpos($tableContent, 'crm-editable">');
        if ($index === FALSE) {
          break;
        }
        else {
          $index += 14;
        }
        $tableContent = substr($tableContent, $index);
        $indexEnd = strpos($tableContent, '</td>');
        if ($indexEnd === FALSE) {
          break;
        }
        $membershipIds[$recordId] = substr($tableContent, 0, $indexEnd);
      }

      $courseNameField = cpdcoursename_get_custom_field();
      $contactId = $object->_contactId;

      $newTable = '<table id="records" class="display"><thead><tr><th>CPD Course</th><th></th></tr></thead>';
      foreach ($membershipIds as $key => $mId) {
        $result = civicrm_api3('Membership', 'get', array(
          'id' => $mId,
        ));
        if (!empty($result['is_error']) || empty($result['id'])) {
          CRM_Core_Session::setStatus('Could not find membership with Id: '. $mId, 'Error');
          $row = '<tr><td>Unknown ID: ' . $mId . '</td>';
        }
        else {
          $courseName = $result['values'][$mId]['custom_' . $courseNameField['id']];
          $mContactId = $result['values'][$mId]['contact_id'];

          $url = CRM_Utils_System::url('civicrm/contact/view/membership', "action=view&reset=1&id={$mId}&cid={$mContactId}");
          $row = '<tr><td><a class="crm-popup" href="' . $url . '">' . $courseName . '</a></td>';
        }
        $row .= '<td><a href="#" class="action-item crm-hover-button delete-custom-row" title="Delete CPD Tutor record"'
          . 'data-delete_params="{&quot;valueID&quot;:'.$key.',&quot;groupID&quot;:&quot;'.$customGroup['id']
          . '&quot;,&quot;contactId&quot;:&quot;'.$contactId.'&quot;'
          . ',&quot;key&quot;:&quot;'.$apiStr.'&quot;}">Delete tutor for course</a></td>';
        $row .= '</tr>';
        $newTable .= $row;
      }
      $newTable .= '</table>';

      $content = substr_replace($content, $newTable, $tableStartIndex, $tableEndIndex - $tableStartIndex);
    }
  }
}


/**
 * Implements hook_coreResourceList
 *
 * @param array $list
 * @param string $region
 */
function aor_civicrm_coreResourceList(&$list, $region) {
  Civi::resources()
    ->addStyleFile('uk.co.mjwconsult.aor', 'css/aor.css', 0, 'page-header')
    ->addScriptFile('uk.co.mjwconsult.aor', 'js/membership.js');
}

/**
 * Called before every database commit
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function aor_civicrm_pre($op, $objectName, $objectId, &$objectRef) {
  switch ($objectName) {
    case 'Individual':
      _aor_civicrm_preUpdateContact($op, $objectId, $objectRef);
      break;
  }
}

/**
 * Called after every database commit
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function aor_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  switch ($objectName) {
    case 'Membership':
      _aor_civicrm_postUpdateMembership($op, $objectRef);
      break;
  }
}

/**
 * Contact handler for hook_civicrm_pre
 * We add an external id (membership number) for the contact here if it doesn't have one.
 *
 * @param $op
 * @param $objectId
 * @param $objectRef
 */
function _aor_civicrm_preUpdateContact($op, $objectId, &$objectRef) {
  switch ($op) {
    case 'view':
    case 'create':
    case 'edit':
    case 'restore':
      $contact = _aor_civicrm_addContactMembershipNumber($objectRef, FALSE);
      if ($contact) {
        $objectRef = $contact;
      }
      break;
  }
}

function _aor_civicrm_getLockFile($filename) {
  $lockfile = sys_get_temp_dir() . "/{$filename}.lock";
  Civi::log()->info($lockfile);
  return $lockfile;
}

function _aor_civicrm_releaseLock($lockFP) {
  Civi::log()->info('Release lock');
  flock($lockFP, LOCK_UN);
  fclose($lockFP);
}

/**
 * Membership handler for hook_civicrm_post
 * We add the external id (membership number) to the latest current membership here.
 * NOTE: It must be done via a callback because the database transaction is not completed when this hook is called!
 *
 * @param $op
 * @param $objectRef
 */
function _aor_civicrm_postUpdateMembership($op, &$objectRef) {
Civi::log()->info($op);
  switch ($op) {
    case 'edit':
    case 'create':
    case 'restore':
      try {
        $membership = civicrm_api3('Membership', 'getsingle', array('id' => $objectRef->id));
      }
      catch (Exception $e) {
        return;
      }
      Civi::log()->info('Adding callback');
      CRM_Core_Transaction::addCallback(CRM_Core_Transaction::PHASE_POST_COMMIT,
        '_aor_civicrm_addContactMembershipNumberToMembership', array(_aor_civicrm_getLatestMembership($membership['contact_id'])));
      break;
  }
}

/**
 * Add an external identifier (membership number) to a contact.
 * @param $contact
 * @param $commit
 *
 * @return array|null
 */
function _aor_civicrm_addContactMembershipNumber($contact, $commit) {
  if (isset($contact['contact_id'])) {
    $params['id'] = $contact['contact_id'];
  }
  elseif (isset($contact['id'])) {
    $params['id'] = $contact['id'];
  }
  else {
    Civi::log()->info('No contact ID found - cannot add external_identifier');
    return NULL;
  }
  $contactRecord = civicrm_api3('Contact', 'getsingle', array('id' => $contact['contact_id']));
  if (!empty($contactRecord['external_identifier'])) {
    Civi::log()->info($contact['id'] . ': Already has external_identifier set');
    return NULL;
  }

  while(!lock_acquire("aor_civicrm_addcontactmembershipnumber", 5)){
	  lock_wait("aor_civicrm_addcontactmembershipnumber", 3);
  }
  
  Civi::log()->info($contact['id'] . ': Got lock for new membership number');
  $nextMembershipNo = CRM_Aor_Utils::getSettings('aor_next_membership_number');
  if ($nextMembershipNo > 499999) {
    Civi::log()
      ->warning('uk.co.mjwconsult.aor not creating new AoR membership number as it would cause duplicate >= 500000');
    lock_release("aor_civicrm_addcontactmembershipnumber");    // release the lock
    return NULL;
  }

  $contact['external_identifier'] = $nextMembershipNo;
  if ($commit) {
    Civi::log()->info($contact['id'] . ': Saving new membership number');
    $contact = civicrm_api3('Contact', 'create', $contact);
  }
  // Save the next available membership number
  CRM_Aor_Utils::setSetting($nextMembershipNo + 1, 'aor_next_membership_number');
  lock_release("aor_civicrm_addcontactmembershipnumber");    // release the lock

  return $contact;
}

/**
 * Add the external identifier (membership number) to the latest current membership
 * @param $membership
 *
 * @return null
 */
function _aor_civicrm_addContactMembershipNumberToMembership($membership) {
	
  if(!lock_acquire("addcontactmembershipnumbertomembership", 5)) {
    Civi::log()->info('Not updating membership numbers, lock in progress');
    return;
  }

  Civi::log()->info('addContactMembershipNumberToMembership triggered');
  try {
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'id' => $membership['contact_id'],
    ));
  }
  catch (Exception $e) {
    Civi::log()->warning('Could not get contact from membership. ' . $e->getMessage());
    lock_release("addcontactmembershipnumbertomembership");
    return NULL;
  }

  // if custom_35 already set, don't set it again
  $excludeId = NULL;
  if (isset($membership[_aor_getMembershipNoCustomField()]) && ($membership[_aor_getMembershipNoCustomField()] == $contact['external_identifier'])) {
    $excludeId = $membership['id'];
  }
  // Clear membership numbers from all other memberships
  _aor_civicrm_clearMembershipsMembershipNo($membership['contact_id'], $excludeId);

  if ($excludeId) {
    lock_release("addcontactmembershipnumbertomembership");
    return NULL;
  }

  $membershipParams['id'] = $membership['id'];
  $membershipParams['contact_id'] = $membership['contact_id'];
  $membershipParams[_aor_getMembershipNoCustomField()] = $contact['external_identifier'];

  try {
    Civi::log()->info('about to create/update membership');
    $membership = civicrm_api3('Membership', 'create', $membershipParams);
  } catch (Exception $e) {
    Civi::log()
      ->info('uk.co.mjwconsult.aor: Unable to update field '. _aor_getMembershipNoCustomField() . ' for membership id: ' . (isset($membership['id']) ? $membership['id'] : NULL) . ' Error: ' . $e->getMessage());
    lock_release("addcontactmembershipnumbertomembership");
    return NULL;
  }
  
  lock_release("addcontactmembershipnumbertomembership");
}

/**
 * implement the hook to customize the summary view
 *
 * This checks and creates external id (membership number) for contact and latest membership if not already defined.
 */
function aor_civicrm_pageRun( &$page ) {
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_Summary') {
    // Generate external identifier if none defined (prefix contactId with "A")
    $contactId = $page->getVar('_contactId');
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'contact_id' => $contactId,
    ));
    $updatedContact = _aor_civicrm_addContactMembershipNumber($contact, TRUE);
    _aor_civicrm_addContactMembershipNumberToMembership(_aor_civicrm_getLatestMembership($contact['id']));
    if ($updatedContact) {
      // Refresh the contact summary
      $url = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$contactId}");
      CRM_Utils_System::redirect($url);
    }
  }  
}

function aor_civicrm_tokens( &$tokens ) {
  $tokens['event'] = array(
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

  $tokens['member'] = array(
    'member.name' => ts('Membership Name'),
    'member.course_name' => ts('Membership Course Name'),
    'member.end_date' => ts('Membership End Date'),
    'member.start_date' => ts('Membership Start Date'),
    'member.join_date' => ts('Membership Join Date'),
    'member.qty' => ts("Membership Qty"),
    'member.totalnetamount' => ts("Membership Total Net Amount"),
    'member.totaltaxamount' => ts("Membership Total Tax Amount"),
    'member.totalamount' => ts("Membership Total Amount"),
  );

  $tokens['cpd'] = array(
    'cpd.course_name' => ts('CPD Course Name'),
    'cpd.end_date' => ts('CPD End Date'),
    'cpd.start_date' => ts('CPD Start Date'),
    'cpd.join_date' => ts('CPD Join Date'),
    'cpd.qty' => ts("CPD Qty"),
    'cpd.totalnetamount' => ts("CPD Total Net Amount"),
    'cpd.totaltaxamount' => ts("CPD Total Tax Amount"),
    'cpd.totalamount' => ts("CPD Total Amount"),
  );

  $tokens['advertiser'] = array(
    'advertiser.course_name' => ts('Advertiser Course Name'),
    'advertiser.end_date' => ts('Advertiser End Date'),
    'advertiser.start_date' => ts('Advertiser Start Date'),
    'advertiser.join_date' => ts('Advertiser Join Date'),
    'advertiser.qty' => ts("Advertiser Qty"),
    'advertiser.totalnetamount' => ts("Advertiser Total Net Amount"),
    'advertiser.totaltaxamount' => ts("Advertiser Total Tax Amount"),
    'advertiser.totalamount' => ts("Advertiser Total Amount"),
  );
}

function aor_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  foreach ($cids as $key => $contactId) {
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
        ));

        $member = $nonmember = $total = array();
        foreach ($participantPayments['values'] as $payment) {
          $lineItems = civicrm_api3('LineItem', 'get', array(
            'contribution_id' => $payment['contribution_id'],
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

        foreach ($cids as $cid) {
          $values[$cid] = empty($values[$cid]) ? $event : $values[$cid] + $event;
        }
      }
    }

    if (!empty($tokens['member']) || !empty($tokens['cpd']) || !empty($tokens['advertiser'])) {
      if (!empty($values[$contactId]['membership_id'])) {
        $mid = $values[$contactId]['membership_id'];
      }
      else {
        $mid = CRM_Utils_Request::getValue('mid', $_REQUEST);
      }
      if (empty($mid)) {
        continue;
      }

      try {
        $membershipRecord = civicrm_api3('Membership', 'getsingle', array('id' => $mid));
      } catch (Exception $e) {
        continue;
      }

      $membershipPayments = civicrm_api3('MembershipPayment', 'get', array(
        'membership_id' => $mid,
      ));

      $member = $total = array();
      foreach ($membershipPayments['values'] as $payment) {
        $lineItems = civicrm_api3('LineItem', 'get', array(
          'contribution_id' => $payment['contribution_id'],
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

      if (_aor_is_membership($mid)) {
        $membership = array(
          'member.name' => CRM_Utils_Array::value('membership_name', $membershipRecord),
          'member.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'member.end_date' => CRM_Utils_Array::value('end_date', $membershipRecord),
          'member.start_date' => CRM_Utils_Array::value('start_date', $membershipRecord),
          'member.join_date' => CRM_Utils_Array::value('join_date', $membershipRecord),
          'member.qty' => $member['qty'],
          'member.totalnetamount' => CRM_Utils_Money::format($total['line_total']),
          'member.totaltaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'member.totalamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
        );
      }
      elseif (_aor_is_cpd_membership($mid)) {
        $membership = array(
          'cpd.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'cpd.end_date' => CRM_Utils_Array::value('end_date', $membershipRecord),
          'cpd.start_date' => CRM_Utils_Array::value('start_date', $membershipRecord),
          'cpd.join_date' => CRM_Utils_Array::value('join_date', $membershipRecord),
          'cpd.qty' => $member['qty'],
          'cpd.totalnetamount' => CRM_Utils_Money::format($total['line_total']),
          'cpd.totaltaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'cpd.totalamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
        );
      }
      elseif (_aor_is_advertiser_membership($mid)) {
        $membership = array(
          'advertiser.course_name' => CRM_Utils_Array::value('custom_34', $membershipRecord),
          'advertiser.end_date' => CRM_Utils_Array::value('end_date', $membershipRecord),
          'advertiser.start_date' => CRM_Utils_Array::value('start_date', $membershipRecord),
          'advertiser.join_date' => CRM_Utils_Array::value('join_date', $membershipRecord),
          'advertiser.qty' => $member['qty'],
          'advertiser.totalnetamount' => CRM_Utils_Money::format($total['line_total']),
          'advertiser.totaltaxamount' => CRM_Utils_Money::format($total['tax_amount']),
          'advertiser.totalamount' => CRM_Utils_Money::format($total['line_total'] + $total['tax_amount']),
        );
      }

      $values[$contactId] = empty($values[$contactId]) ? $membership : $values[$contactId] + $membership;
    }
  }
}

function aor_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
  //create a Send Invoice link with the context of the participant's order ID (a custom participant field)
  switch ($objectName) {
    case 'Membership':
      switch ($op) {
        case 'membership.tab.row':
        case 'membership.selector.row':
          $mid = $values['id'];
          $cid = $values['cid'];

          $links[] = array(
            'name' => ts('Print Letter'),
            'title' => ts('Print Letter'),
            'url' => 'civicrm/activity/pdf/add',
            'qs' => "action=add&reset=1&cid={$cid}&selectedChild=activity&atype=22&mid={$mid}",
          );
          $links[] = array(
            'name' => ts('Send Email'),
            'title' => ts('Send Email'),
            'url' => 'civicrm/activity/email/add',
            'qs' => "action=add&reset=1&cid={$cid}&selectedChild=activity&atype=3&mid={$mid}",
          );
      }
      break;
    case 'Participant':
      switch ($op) {
        case 'participant.selector.row':
          $cid = $values['cid'];
          $pid = $values['id'];

          $links[] = array(
            'name' => ts('Print Letter'),
            'title' => ts('Print Letter'),
            'url' => 'civicrm/activity/pdf/add',
            'qs' => "action=add&reset=1&cid={$cid}&selectedChild=activity&atype=22&pid={$pid}",
          );
          $links[] = array(
            'name' => ts('Send Email'),
            'title' => ts('Send Email'),
            'url' => 'civicrm/activity/email/add',
            'qs' => "action=add&reset=1&cid={$cid}&selectedChild=activity&atype=3&pid={$pid}",
          );
          break;
      }
  }
}

/**
 * Clear membership number field for all memberships for contact id.
 * @param $cid
 */
function _aor_civicrm_clearMembershipsMembershipNo($cid, $excludeId = NULL) {
  static $flag = 0;
  
  if($flag){
	  return;
  }
  else{
	  $flag = 1;
  }
  
  $memberships = civicrm_api3('Membership', 'get', array('contact_id' => $cid));
  Civi::log()->info('Membership count: ' . $memberships['count']);
  if (!empty($memberships['count'])) {
    foreach ($memberships['values'] as $membership) {
      if ($membership['id'] == $excludeId) {
        Civi::log()->info($membership['id'] . ': Excluding membership already set');
        continue;
      }
      else {
        Civi::log()->info($membership['id'] . ': Processing clear membership');
      }
	  
	  $updatedmembership['contact_id'] = $membership['contact_id'];
	  $updatedmembership['id'] = $membership['id'];	  
	  $updatedmembership[_aor_getMembershipNoCustomField()] = '';
		  
	  Civi::log()->info($membership['id'] . ': Updating membership');
	  civicrm_api3('Membership', 'create', $updatedmembership);
    }
  }
}

/**
 * Get the latest membership for contact id.
 * @param $cid
 *
 * @return array|null
 */
function _aor_civicrm_getLatestMembership($cid) {
  $params = array(
    'contact_id' => $cid,
    'sequential' => 1,
    'api.membership_type.getsingle' => 1,
    'options' => array('limit' => 1, 'sort' => 'end_date DESC'),
  );

  // Only get memberships with financial type "Member Dues"
  try {
    $membershipTypes = civicrm_api3('MembershipType', 'get', array(
      'financial_type_id' => _aor_getMembershipFinancialTypes(),
	  'options' => array('limit' => 0)
    ));
  }
  catch (Exception $e) {
    Civi::log()->info('uk.co.mjwconsult.aor: Invalid financial type ' . $e->getMessage());
    return NULL;
  }

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

function aor_civicrm_buildForm($formName, &$form) {
  $bob =1;
  switch ($formName) {
    case 'CRM_Event_Form_ManageEvent_Location':
      Civi::resources()->addScriptFile('uk.co.mjwconsult.aor', 'js/address.js');
      break;
    case 'CRM_Contribute_Form_Contribution_Main':
      if ($form->_id == 5) {
        // Membership renewal contribution page
        Civi::resources()
          ->addScriptFile('uk.co.mjwconsult.aor', 'js/contribution_form_5.js');
      }
      break;
    case 'CRM_Contact_Form_Task_Email':
    case 'CRM_Contact_Form_Task_PDF':
      $pid = CRM_Utils_Request::getValue('pid', $_REQUEST);
      $mid = CRM_Utils_Request::getValue('mid', $_REQUEST);
      $form->add('hidden', 'pid');
      $form->add('hidden', 'mid');
      // dynamically insert a template block in the page
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "CRM/aor/hiddenfields.tpl"
      ));
      $defaults['pid'] = $pid;
      $defaults['mid'] = $mid;
      $form->setDefaults($defaults);
      break;
  }
}

/**
 * Get the CPD Tutor custom group
 */
$_cpdtutor_custom_group = NULL; // static, global variable
function cpdtutor_get_custom_group()
{
  global $_cpdtutor_custom_group;
  if ($_cpdtutor_custom_group === NULL) {
    // load custom field data
    $_cpdtutor_custom_group = civicrm_api3('CustomGroup', 'getsingle', array('name' => "CPD_Tutor"));
  }
  return $_cpdtutor_custom_group;
}

/**
 * Get the CPD Course name custom field
 */
$_cpdcoursename_custom_field = NULL; // static, global variable
function cpdcoursename_get_custom_field()
{
  global $_cpdcoursename_custom_field;
  if ($_cpdcoursename_custom_field === NULL) {
    // load custom field data
    $_cpdcoursename_custom_field = civicrm_api3('CustomField', 'getsingle', array('name' => "Course_name"));
  }
  return $_cpdcoursename_custom_field;
}

/**
 * Get the CPD Course name custom field
 */
$_cpdcourseid_custom_field = NULL; // static, global variable
function cpdcourseid_get_custom_field()
{
  global $_cpdcourseid_custom_field;
  if ($_cpdcourseid_custom_field === NULL) {
    // load custom field data
    $_cpdcourseid_custom_field = civicrm_api3('CustomField', 'getsingle', array('name' => "CPD_Course_ID"));
  }
  return $_cpdcourseid_custom_field;
}

function _aor_is_membership($mid) {
  $params = array(
    'id' => $mid,
    'api.membership_type.getsingle' => 1,
  );

  // Only get memberships with financial type "Member Dues"
  try {
    $membershipTypes = civicrm_api3('MembershipType', 'get', array(
      'financial_type_id' => _aor_getMembershipFinancialTypes(),
    ));
  }
  catch (Exception $e) {
    // One of the financial_types is not defined, probably on a dev environment
    Civi::log()->debug('_aor_is_membership: ' . $e->getMessage());
    return TRUE; // Assume all membership types are valid
  }
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

function _aor_is_cpd_membership($mid) {
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

function _aor_is_advertiser_membership($mid) {
  $params = array(
    'id' => $mid,
    'api.membership_type.getsingle' => 1,
  );

  // Only get memberships with financial type "Member Dues"
  $membershipTypes = civicrm_api3('MembershipType', 'get', array(
    'financial_type_id' => _aor_getAdvertiserFinancialTypes(),
  ));
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
