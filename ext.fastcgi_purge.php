<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'fastcgi_purge/config.php';

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Purge Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Kevin Cupp
 * @link		http://kevincupp.com
 */

class Fastcgi_purge_ext
{	
	public $description    = 'Sends purge request after entry submission and deletion.';
	public $docs_url       = '';
	public $name           = FASTCGI_PURGE_NAME;
	public $settings_exist = 'n';
	public $version        = FASTCGI_PURGE_VERSION;
	
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->tablename = strtolower(FASTCGI_PURGE_NAME) . '_rules';
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		$hooks = array(
		  'entry_submission_end'	=> 'send_purge_request',
		  'delete_entries_end'		=>'send_purge_request'
		);
		
		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> '',
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);
			
			ee()->db->insert('extensions', $data);
		}
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * Sends purge request to Varnish when registered EE hooks are triggered
	 *
	 * @param 
	 * @return 
	 */
	public function send_purge_request($id,$meta,$data)
	{

		ee()->load->helper('fastcgi');
		
		//get patterns for this channel
		ee()->db->select('*');
		ee()->db->where('channel_id',(int) $meta['channel_id']);
		ee()->db->where('site_id',(int) $meta['site_id']);
		$channelPatterns = ee()->db->get_where($this->tablename)->result_array();
		
		//if no patterns this may mean patterns aren't configured therefore revert to old behaviour of clearing everything 
		if( count($channelPatterns)==0)
		{
			//are there any patterns at all? 
			ee()->db->select('*');
			$channelPatterns = ee()->db->get_where($this->tablename)->result_array();
			if(count($channelPatterns)==0) {
				$channelPatterns = array( array('pattern', '*') );
			}
			else {
				return false; //patterns are configured but not for this channel.
			}
		}
		

		//and loop through patterns for each
		foreach($channelPatterns as $pattern)
		{
			$_pattern = str_replace('{url_title}',$meta['url_title'],$pattern['pattern']); //only str replacing url title at this point
			$purge_url = preg_replace('/\/$/','',$url).'/'.preg_replace('/^\//','',$_pattern);
			fastcgi_purge(ee()->config->item('fastcgi_cache_dir'), $purge_url);
		}

	}

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.fastcgi_purge.php */
/* Location: /system/expressionengine/third_party/purge/ext.purge.php */
