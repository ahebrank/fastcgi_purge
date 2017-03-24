<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('fastcgi_purge'))
{
	/**
	 * Sends purge request to fastcgi through CURL
	 */
	function fastcgi_purge($cache_dir, $purge_url) {
		$cache_dir = rtrim($cache_dir, '/');
		if (!is_dir($cache_dir)) {
			return FALSE;
		}
		// just an extra check
		if (strpos($cache_dir, '/cache') === FALSE) {
			return FALSE;
		}

		if (empty($purge_url)) {
			array_map('unlink', glob($cache_dir . "/*/*/*"));
		}
		else {
			$hash = md5($purge_url);
			$cache_file = $cache_dir . '/' . substr($hash, -1) . '/' . substr($hash,-3,2) . '/' . $hash;
			if (file_exists($cache_file)) {
				unlink($cache_file);
			}
		}
		return true;
	}
}
