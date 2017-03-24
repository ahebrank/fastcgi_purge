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
 * Purge Accessory
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Accessory
 * @author		Kevin Cupp
 * @link		http://kevincupp.com
 */
 
class Fastcgi_purge_acc
{	
	public $name        = FASTCGI_PURGE_NAME;
	public $id          = 'fastcgi_purge';
	public $version     = FASTCGI_PURGE_VERSION;
	public $description = 'Provides a place to manually send a purge request to fastcgi.';
	public $sections    = array();
	
	/**
	 * Set Sections
	 */
	public function set_sections()
	{	
		$data['request_url'] = html_entity_decode(BASE.AMP.'C=addons_accessories'.AMP.'M=process_request'.AMP.'accessory=fastcgi_purge'.AMP.'method=process_purge_request');
		$this->sections['Purge Fastcgi'] = ee()->load->view('accessory_purge_fastcgi', $data, TRUE);
	}
	
	/**
	 * Handles AJAX request from control panel accessory to send purge request to Varnish
	 */
	public function process_purge_request()
	{
		if (AJAX_REQUEST)
		{
			ee()->load->helper('fastcgi');
			fastcgi_purge(ee()->config->item('fastcgi_cache_dir'), $_POST['purge_url']);
			die(); 
		}
	}
}
 
/* End of file acc.purge.php */
/* Location: /system/expressionengine/third_party/purge/acc.purge.php */
