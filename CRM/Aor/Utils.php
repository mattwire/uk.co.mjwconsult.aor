<?php

class CRM_Aor_Utils {
  CONST PREFERENCES_NAME = 'Aor Preferences';

  /**
   * Returns settings
   */
  static function getSettings($name = NULL) {
    return CRM_Core_BAO_Setting::getItem(CRM_Aor_Utils::PREFERENCES_NAME, $name);
  }

  static function setSetting($value, $name) {
    CRM_Core_BAO_Setting::setItem($value, CRM_Aor_Utils::PREFERENCES_NAME, $name);
  }
}
