<?php
/******************************
 * filename:    modules/zohodeskSettings/actions/actions.php
 * description:
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ZohodeskSettingsActions extends SkinnyActions_HelpDeskIntegration {

    public function __construct()
    {
    }

  /**
   * The actions index method
   * @param array $request
   * @return array
   */
    public function executeIndex($request)
    {
       $data = array();
        return $data;
    }

}
