<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( "SmackZohoSupportApi" ) )
{
	class SmackZohoSupportApi{

		/******************************************************************************************
		 * Copyright (C) Smackcoders 2016 - All Rights Reserved
		 * Unauthorized copying of this file, via any medium is strictly prohibited
		 * Proprietary and confidential
		 * You can contact Smackcoders at email address info@smackcoders.com.
		 *******************************************************************************************/

		public $zohosupporturl;

		public function __construct()
		{
			$this->zohosupporturl = "https://support.zoho.com/api/xml/";
			$zohoconfig = get_option("smack_whi_zohodesk1_settings");
			$this->access_token = (isset($zohoconfig['access_token'])) ? $zohoconfig['access_token'] : '';
			$this->refresh_token = (isset($zohoconfig['refresh_token'])) ? $zohoconfig['refresh_token'] : '';
			$this->callback = (isset($zohoconfig['callback'])) ? $zohoconfig['callback'] : '';
			$this->client_id = (isset($zohoconfig['key'])) ? $zohoconfig['key'] : '';
			$this->client_secret = (isset($zohoconfig['secret'])) ? $zohoconfig['secret'] : '';
			//$this->org_id = $zohoconfig['org_id'];
			$this->org_id = (isset($zohoconfig['org_id'])) ? $zohoconfig['org_id'] : '';
			$this->domain = $zohoconfig['domain'];
			update_option('smack_whi_zohodesk1_settings', $zohoconfig);
		}

		public function addrecords( $modulename, $methodname, $authkey ,$portalname , $departmentname , $xmlData="" , $extraParams = "" )
		{	
			$uri = $this->zohosupporturl . $modulename . "/".$methodname;
			/* Append your parameters here */
			$postContent = "&authtoken={$authkey}";//Give your authtoken
			$postContent .= "&scope=supportapi";
			$postContent .= "&portal={$portalname}";
			$postContent .= "&department={$departmentname}";
			$postContent .= "&xml={$xmlData}";
			if($extraParams != "")
			{
				$postContent .= $extraParams;
			}
			$args = array(
					'method' => 'POST',
					'sslverify' => false,
					'body' => $postContent
				     );
			$response =  wp_remote_post($uri, $args ) ;
			$result = wp_remote_retrieve_body($response);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			return $result_array;

		}

		public function getrecords( $modulename, $methodname, $authkey , $portalname ,$departmentname , $selectColumns ="" , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohosupporturl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=supportapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			$postContent .= "&portal={$portalname}";
			$postContent .= "&department={$departmentname}";
			if($selectColumns == "")
			{
				$postContent .= "&selectColumns=All";
			}
			else
			{
				$postContent .= "&selectColumns={$modulename}( {$selectColumns} )";
			}

			if($extraParams != "")
			{
				$postContent .= $extraParams;
			}
			$postContent .= "&xml={$xmlData}";
			$args = array(
					'method' => 'POST',
					'sslverify' => false,
					'body' => $postContent
				     );
			$response =  wp_remote_post($uri, $args ) ;	
			$result = wp_remote_retrieve_body($response);
			$str = preg_replace('#<!\[CDATA\[(.+?)\]\]>#s', '$1', $result);
			$xml = simplexml_load_string($str);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			return $result_array;
		}

		public function getAuthenticationToken( $username , $password  )
		{
			$username = urlencode( $username );
			$password = urlencode( $password );
			$param = "SCOPE=ZohoSUPPORT/supportapi,ZohoSearch/SearchAPI&EMAIL_ID=".$username."&PASSWORD=".$password;
			$uri = "https://accounts.zoho.com/apiauthtoken/nb/create";
			$args = array(
					'method' => 'POST',
					'sslverify' => false,
					'body' => $param
				     );
			$response =  wp_remote_post($uri, $args ) ;
			$result = wp_remote_retrieve_body($response);
			$anArray = explode("\n",$result);
			$authToken = explode("=",$anArray['2']);
			$cmp = strcmp($authToken['0'],"AUTHTOKEN");
			if ($cmp == 0)
			{
				$return_array['authToken'] = $authToken['1'];
			}
			$return_result = explode("=" , $anArray['3'] );
			$cmp1 = strcmp($return_result['0'],"RESULT");
			if($cmp1 == 0)
			{
				$return_array['result'] = $return_result['1'];
			}
			if($return_result[1] == 'FALSE'){
				$return_cause = explode("=",$anArray[2]);
				$cmp2 = strcmp($return_cause[0],'CAUSE');
				if($cmp2 == 0)
					$return_array['cause'] = $return_cause[1];
			}
			return $return_array;
		}

		/**
		 * ZohoGet_Getaccess
		 *
		 * @param  mixed $config
		 * @param  mixed $code
		 *
		 * @return void
		 */
		public function ZohoGet_Getaccess( $config , $code ) {
			//$token_url = "https://accounts.zoho".$this->domain."/oauth/v2/token?";
			
			$params = "code=" .$code
				. "&redirect_uri=" . $this->callback
				. "&client_id=" . $this->client_id
				. "&client_secret=" . $this->client_secret
				. "&grant_type=authorization_code"
				. "&scope=Desk.settings.READ,Desk.tickets.READ,Desk.basic.READ,Desk.tickets.CREATE,Desk.tickets.UPDATE,Desk.contacts.CREATE,Desk.contacts.UPDATE,Desk.contacts.READ";
			
			$args = array(
				'method' => 'POST',
				'sslverify' => false,
				'body' => $params,
				'headers' => false
				);
				
				$result = wp_remote_post( 'https://accounts.zoho.com/oauth/v2/token?', array(
					'body' => $params
				) );
				$response = json_decode(wp_remote_retrieve_body( $result ), TRUE );;	
				$responceData = json_decode(wp_remote_retrieve_body( $response ), TRUE );
				return $response;
		}

		/**
		* refresh_token
		*
		* @return void
		*/
		public function refresh_token() {
			$token_url = "https://accounts.zoho".$this->domain."/oauth/v2/token?";
		
			$params = "refresh_token=" . $this->refresh_token
				. "&client_id=" . $this->client_id
				. "&client_secret=" . $this->client_secret
				. "&grant_type=refresh_token"
				. "&scope=Desk.settings.READ,Desk.tickets.READ,Desk.basic.READ,Desk.tickets.CREATE,Desk.tickets.UPDATE,Desk.contacts.CREATE,Desk.contacts.UPDATE,Desk.contacts.READ"
				. "&redirect_uri=" . $this->callback;

			$args = array(
				'method' => 'POST',
				'sslverify' => false,
				'body' => $params,
				'headers' => false
				);
				
			$result =  wp_remote_post($token_url, $args ) ;
			$response = wp_remote_retrieve_body($result);
			$status = wp_remote_retrieve_response_code($result);
			
			if ( $status != 200 ) {
				die("Error: call to token URL $token_url failed with status $status, response $response");
			}
			$response = json_decode($response, true);
		
			$zohocrm_credentials = get_option('smack_whi_zohodesk1_settings');

			$zohocrm_credentials['access_token']=$response['access_token'];
			$zohocrm_credentials['api_domain']=$response['api_domain'];
			$zohocrm_credentials['refresh_token']=$zohocrm_credentials['refresh_token'];
			$zohocrm_credentials['key']=$zohocrm_credentials['key'];
			$zohocrm_credentials['secret']=$zohocrm_credentials['secret'];
			$zohocrm_credentials['org_id']=$zohocrm_credentials['org_id'];
			$zohocrm_credentials['callback']=$zohocrm_credentials['callback'];
			$zohocrm_credentials['domain']=$zohocrm_credentials['domain'];
		
			update_option("smack_whi_zohodesk1_settings",$zohocrm_credentials);
		}

		public function Zoho_Getuser(){
			$url = "https://desk.zoho".$this->domain."/api/v1/agents";	
			
			$args = array(
					'headers' => array(
						'orgId' => $this->org_id,
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
					);
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );	
			$body = json_decode($response, true);
			return $body;
		}

		public function Zoho_getFields($module){
			$module = strtolower($module);

			$url = "https://desk.zoho".$this->domain."/api/v1/organizationFields?module=$module";

			$args = array(
					'headers' => array(
						'orgId' => $this->org_id,
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
					);
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}

		public function Zoho_getTickets($module){
			
			if($module == 'tickets'){
				//$url = "https://desk.zoho.com/api/v1/tickets?include=contacts,assignee,departments,team,isRead";
				$url = "https://desk.zoho".$this->domain."/api/v1/tickets";

			}elseif($module == 'contacts'){
				$url = "https://desk.zoho".$this->domain."/api/v1/contacts";
			}
			
			$args = array(
					'headers' => array(
						'orgId' => $this->org_id,
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
					);
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}

		public function Zoho_getDepartments($name){	
			$url = "https://desk.zoho".$this->domain."/api/v1/departments?isEnabled=true";	
			$args = array(
					'headers' => array(
						'orgId' => $this->org_id,
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
					);
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);

			if($body['errorCode']=='INVALID_OAUTH'){
				$this->refresh_token();
				$this->Zoho_getDepartments($name);
			}
			
			foreach($body['data'] as $value){
				if($value['name'] == $name){
					$dep_id  = $value['id'];
				}else{
					$dep_id = $value['id'];
				}
			}
			return $dep_id;
		}

		public function Zoho_CreateRecord($module,$data_array){
			$module = strtolower($module);
		
			$apiUrl = "https://desk.zoho".$this->domain."/api/v1/$module";	
			$fields = json_encode($data_array);

			$headers = array( 'Authorization' => 'Zoho-oauthtoken '. $this->access_token,
								'orgId' => $this->org_id,
								'Content-Type' => 'application/json'
						);
			
			$args = array(
				'method' => 'POST',
				'sslverify' => false,
				'body' => $fields,
				'headers' => $headers
				);
				
			$result = wp_remote_post($apiUrl, $args ) ;
			$response = wp_remote_retrieve_body($result);
			$http_code = wp_remote_retrieve_response_code($result);

			if ( $http_code != 200 ) {
				die("Zohodesk encountered an error. CODE: " . $http_code . " Response: " . $response);
			}

			$result_array = json_decode($response,true);
			// if($extraParams != "")
			// {
			// 	foreach($extraParams as $field => $path){			
			// 		$this->insertattachment($result_array,$path,$module);
			// 	}
			// }
			return $result_array;
		}

		public function Zoho_createContacts($data_array){
			
			$apiUrl = "https://desk.zoho".$this->domain."/api/v1/contacts";	
			$fields = json_encode($data_array);

			$headers = array( 'Authorization' => 'Zoho-oauthtoken '. $this->access_token,
								'orgId' => $this->org_id,
								'Content-Type' => 'application/json'
						);
			
			$args = array(
				'method' => 'POST',
				'sslverify' => false,
				'body' => $fields,
				'headers' => $headers
				);
				
			$result = wp_remote_post($apiUrl, $args ) ;
			$response = wp_remote_retrieve_body($result);
			$http_code = wp_remote_retrieve_response_code($result);

			if ( $http_code != 200 ) {
				die("Zohodesk encountered an error. CODE: " . $http_code . " Response: " . $response);
			}

			$result_array = json_decode($response,true);
			return $result_array;
		}
	}
}