<?php
/******************************
 * filename:    modules/zendeskSettings/actions/actions.php
 * description:
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ZendeskSettingsActions extends SkinnyActions_HelpDeskIntegration {

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
