<?php
/**
 * @version 1.2
 * @package dinke.net
 * @copyright &copy; 2008 Dinke.net
 * @author Dragan Dinic <dragan@dinke.net>
 */

class Curl_HTTP_Client
{
	/**
	 * set debug to true in order to get usefull output
	 * @access private
	 * @var string
	 */
	var $debug = false;

	/**
	 * Contain last error message if error occured
	 * @access private
	 * @var string
	 */
	var $error_msg;


	/**
	 * SmackVtigerApi constructor
	 * @param boolean debug
	 * @access public
	 */
	function Curl_HTTP_Client($debug = false)
	{
		$this->debug = $debug;
	}

	/**
	 * Send post data to target URL	 
	 * return data returned from url or false if error occured
	 * @param string url
	 * @param mixed post data (assoc array ie. $foo['post_var_name'] = $value or as string like var=val1&var2=val2)
	 * @param string ip address to bind (default null)
	 * @return string data
	 * @access public
	 */
	function send_post_data($url, $postdata, $ip=null, $timeout=10)
	{
		//generate post string
		$post_array = array();
		if(is_array($postdata))
		{		
			foreach($postdata as $key=>$value)
			{
				$post_array[] = urlencode($key) . "=" . urlencode($value);
			}

			$post_string = implode("&",$post_array);

			if($this->debug)
			{
				echo "Url: $url\nPost String: $post_string\n";
			}
		}
		else 
		{
			$post_string = $postdata;
		}
        $response = wp_remote_post( $url, array(
						'method' => 'POST',
						'sslverify' => false,
						'body' => $post_string
						)
					);	
		$result = $response['body'];
		return $result;
	}

	/**
	 * fetch data from target URL	 
	 * return data returned from url or false if error occured
	 * @param string url	 
	 * @param string ip address to bind (default null)
	 * @return string data
	 * @access public
	 */
	function fetch_url($url, $ip=null, $timeout=5)
	{
		$response =  wp_remote_get($url); 
		$result =  wp_remote_retrieve_body($response);
		return $result;
	}
}
?>
