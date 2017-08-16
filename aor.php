<?php

require_once 'aor.civix.php';

/**
 * Returns array of financial type Ids used for membership. Use in api calls relating to membership
 * @return array
 */
function _aor_getMembershipFinancialTypes() {
  return array('IN' => array(2, 42, 72, 81)); // "Member Dues", Historical Member dues, membership inc vat, membership no vat
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

function aor_civicrm_pre($op, $objectName, $objectId, &$objectRef) {
  switch ($objectName) {
    case 'Individual':
      _aor_civicrm_preUpdateContact($op, $objectId, $objectRef);
      break;
  }
}

function aor_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  switch ($objectName) {
    case 'Membership':
      _aor_civicrm_postUpdateMembership($op, $objectRef);
      break;
  }
}

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

function _aor_civicrm_addContactMembershipNumber($contact, $commit) {
  if (!empty($contact['external_identifier'])) {
    return NULL;
  }

  $fp = fopen(CRM_Utils_File::tempnam(), "r+");
  if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    $nextMembershipNo = CRM_Aor_Utils::getSettings('aor_next_membership_number');
    if ($nextMembershipNo > 499999) {
      Civi::log()
        ->warning('uk.co.mjwconsult.aor not creating new AoR membership number as it would cause duplicate >= 500000');
      flock($fp, LOCK_UN);    // release the lock
      return NULL;
    }

    $contact['external_identifier'] = $nextMembershipNo;
    if ($commit) {
      $contact = civicrm_api3('Contact', 'create', $contact);
    }
    // Save the next available membership number
    CRM_Aor_Utils::setSetting($nextMembershipNo + 1, 'aor_next_membership_number');
    flock($fp, LOCK_UN);    // release the lock

    return $contact;
  }
}

function _aor_civicrm_postUpdateMembership($op, &$objectRef) {
  switch ($op) {
    case 'view':
    case 'create':
    //case 'edit':
    case 'restore':
      $membership = civicrm_api3('Membership', 'getsingle', array('id' => $objectRef->id));
      _aor_civicrm_addContactMembershipNumberToMembership($membership['contact_id']);
      break;
  }
}

function _aor_civicrm_addContactMembershipNumberToMembership($contactId) {
  try {
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'id' => $contactId,
    ));
  }
  catch (Exception $e) {
    Civi::log()->warning('Could not get contact id ' . $contactId . ' for membership ' . $e->getMessage());
    return NULL;
  }

  // Add membership number to latest current membership record
  $membership = _aor_getLatestMembership($contactId);

  // if custom_35 already set, don't set it again
  if ($membership[_aor_getMembershipNoCustomField()] == $contact['external_identifier']) {
    return NULL;
  }
  // Clear membership numbers from all other memberships
  _aor_civicrmClearMembershipsMembershipNo($contactId);

  $membership[_aor_getMembershipNoCustomField()] = $contact['external_identifier'];
  try {
    $membership = civicrm_api3('membership', 'create', $membership);
    return CRM_Utils_Array::first($membership['values']);
  } catch (Exception $e) {
    Civi::log()
      ->info('uk.co.mjwconsult.aor: Unable to update field '. _aor_getMembershipNoCustomField() . ' for membership id: ' . $membership['id'] . ' Error: ' . $e->getMessage());
    return NULL;
  }
}

/**
 * implement the hook to customize the summary view
 */
function aor_civicrm_pageRun( &$page ) {
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_Summary') {
    // Generate external identifier if none defined (prefix contactId with "A")
    $contactId = $page->getVar('_contactId');
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'contact_id' => $contactId,
    ));
    $updatedContact = _aor_civicrm_addContactMembershipNumber($contact, TRUE);
    _aor_civicrm_addContactMembershipNumberToMembership($contact['id']);
    if ($updatedContact) {
      // Refresh the contact summary
      CRM_Utils_System::redirect($_SERVER['REQUEST_URI']);
    }
  }
}

function _aor_civicrmClearMembershipsMembershipNo($cid) {
  $memberships = civicrm_api3('Membership', 'get', array('contact_id' => $cid));
  if (!empty($memberships['count'])) {
    foreach ($memberships['values'] as $membership) {
      if (!empty($membership[_aor_getMembershipNoCustomField()])) {
        $membership[_aor_getMembershipNoCustomField()] = '';
        civicrm_api3('Membership', 'create', $membership);
      }
    }
  }
}

function _aor_getLatestMembership($cid) {
  $params = array(
    'version' => 3,
    'contact_id' => $cid,
    'sequential' => 1,
    'api.membership_type.getsingle' => 1,
    'options' => array('limit' => 1, 'sort' => 'end_date DESC'),
  );

  // Only get memberships with financial type "Member Dues"
  try {
    $membershipTypes = civicrm_api3('MembershipType', 'get', array(
      'financial_type_id' => _aor_getMembershipFinancialTypes(),
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
    $membership = civicrm_api3('membership', 'getsingle', $params);
  }
  catch (Exception $e) {
    Civi::log()->info('uk.co.mjwconsult.aor: No membership found ' . $e->getMessage());
    return NULL;
  }
  return $membership;
}

function aor_civicrm_buildForm($formName, &$form) {
  switch ($formName) {
    case 'CRM_Event_Form_ManageEvent_Location':
      CRM_Core_Resources::singleton()->addScriptFile('uk.co.mjwconsult.aor', 'js/address.js');
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
