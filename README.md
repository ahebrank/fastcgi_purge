# FastCGI Purge

FastCGI Purge is an extension for ExpressionEngine 2 that sends a purge request to the Nginx FastCGI cache upon entry submission/deletion. There is also an accessory option to manually send invalidation requests by URL or for the entire cache.

This module operates directly on the cache in the filesystem by hashing URL paths.

This module is based on [Purge](https://github.com/kevincupp/purge.ee2_addon) for Varnish invalidation.

## Nginx setup

As a safety feature, the cache path must have 'cache' in it and be an existing directory. Leave the cache levels as `1:2` to ensure the EE hash function matches the Nginx config.

```
fastcgi_cache_path /etc/nginx/cache levels=1:2 keys_zone=EE:100m inactive=1d; # set cache path lifetime
fastcgi_cache_key "$request_uri"; # this keying is required for individual page purge requests to succeed
fastcgi_cache_use_stale error timeout invalid_header http_500;
fastcgi_ignore_headers Cache-Control Expires Set-Cookie;

server {
	set $skip_cache 0;

	# disable the caching for a bunch of reasons
	if ($request_uri ~ "^/manage/") {
		set $skip_cache 1;
	}
	if ($request_uri ~* "ACT=") {
        set $skip_cache 1;
	}
	if ($request_method = POST) {
		set $skip_cache 1;
	}

	location ~ \.php$ {
		fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_cache_bypass $skip_cache;
	    fastcgi_no_cache $skip_cache;
		fastcgi_cache EE;
		fastcgi_cache_valid  60m;
		# if you want to see HIT vs. MISS
		add_header X-Cache $upstream_cache_status;
	}
}
```

## Module setup

In `config.php`:

```
// should match the location in nginx config
$config['fastcgi_cache_dir'] = '/etc/nginx/cache';
```