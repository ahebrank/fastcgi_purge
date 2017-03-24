<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'fastcgi_purge/config.php';

class Fastcgi_purge_upd {
	
	public $version = FASTCGI_PURGE_VERSION;
	private $tablename;
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{	
		$mod_data = array(
			'module_name'        => FASTCGI_PURGE_NAME,
			'module_version'     => $this->version,
			'has_cp_backend'     => "y",
			'has_publish_fields' => 'n'
		);

		$prefix = ee()->db->dbprefix;
		$tableprefix = strtolower(FASTCGI_PURGE_NAME);
		$this->tablename = strtolower(FASTCGI_PURGE_NAME) . '_rules';
		
		// add the rules table
		$sql[] = "
		CREATE TABLE `{$prefix}{$this->tablename}` (
			`id` int(11) unsigned NOT NULL auto_increment,
			`channel_id` int(4) unsigned NOT NULL,
			`site_id` int(4) unsigned NOT NULL,
	 		`pattern` varchar(255) default NULL,
			PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";

		// run the queries one by one
		foreach ($sql as $query)
		{
			ee()->db->query($query);
		}	
		
		ee()->functions->clear_caching('db');
		ee()->db->insert('modules', $mod_data);
		
		ee()->load->dbforge();

			
		// Enable the extension to prevent redirect erros while installing.
		ee()->db->where('class', 'Fastcgi_purge_ext');
		ee()->db->update('extensions', array('enabled'=>'y'));
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{

		$mod_id = ee()->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> FASTCGI_PURGE_NAME
								))->row('module_id');
		
		ee()->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		ee()->db->where('module_name', FASTCGI_PURGE_NAME)
					 ->delete('modules');
		
		ee()->load->dbforge();
		ee()->dbforge->drop_table($this->tablename);

		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		// If you have updates, drop 'em in here.
		return FALSE;
	}
	

	
}
