<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'fastcgi_purge/config.php';
 
class Fastcgi_purge_mcp {
	
	private $_base_url;
	private $_site_id; 
	private $tablename;
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=fastcgi_purge';
		$this->_site_id = (int) ee()->config->item('site_id');
		
		ee()->cp->set_right_nav(array(
			'module_home'	=> $this->_base_url
		));

		ee()->view->cp_page_title = "Fastcgi Purge: Channel URL Patterns";

		$this->tablename = strtolower(FASTCGI_PURGE_NAME) . '_rules';
	}
	
	


	public function index()
	{
		
		$_data = array();
		
		$_data['action_url'] = $this->_base_url . '&method=save';
				
		ee()->load->model('channel_model');
		$channels_query = ee()->channel_model->get_channels()->result();
		foreach ($channels_query as $channel) 
			$_data['channels'][] = array('channel_title' => $channel->channel_title, 'channel_name' => $channel->channel_name, 'channel_id' => $channel->channel_id );
		
		$_data['rules'] = $this->get();
		
		return ee()->load->view('rules', $_data, TRUE);
		
	}
	
	private function get()
	{
		
		ee()->db->select('*');
		ee()->db->where('site_id', $this->_site_id);
		return ee()->db->get_where($this->tablename)->result_array();
	}
	
	
	public function save()
	{	
	
		$rules = $_POST['rule'];
		$patterns = $_POST['pattern'];
		
		ee()->db->empty_table($this->tablename);
		
		foreach($rules as $key => $channel)
		{
			ee()->db->insert( $this->tablename, array('site_id' => $this->_site_id, 'channel_id' => $channel, 'pattern' => $patterns[$key]) );	
		}

		ee()->functions->redirect($this->_base_url);
	}
	
}
