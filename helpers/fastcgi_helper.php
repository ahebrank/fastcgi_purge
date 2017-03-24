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
			return 0;
		}
		// just an extra check
		if (strpos($cache_dir, '/cache') === FALSE) {
			return 0;
		}

		if (empty($purge_url)) {
			$to_remove = glob($cache_dir . "/*/*/*");
			array_map('unlink', $to_remove);
			return count($to_remove);
		}
		else {
			$hash = md5($purge_url);
			$cache_file = realpath($cache_dir . '/' . substr($hash, -1) . '/' . substr($hash,-3,2) . '/' . $hash);
			// make sure the canonicalized cache directory is still the same
			if (strpos($cache_file, $cache_dir) === 0 && file_exists($cache_file)) {
				unlink($cache_file);
				return 1;
			}
		}
		return 0;
	}
}
