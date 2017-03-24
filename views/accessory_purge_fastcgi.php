<style>
	.fastcgi-purge--inputs > * {
		display: inline-block;
	}
	.fastcgi-purge--message {
		float: right;
	}
</style>
<div class="fastcgi-purge--wrapper">
	<div id="fastcgi_purge_message" class="fastcgi-purge--message">
	</div>
	<div class="fastcgi-purge--inputs">
		<label>Enter relative URL or leave blank to purge everything.
			<input id="purge_url" type="text" placeholder="/example-url">
		</label>
		<input type="submit" id="purge_fastcgi" value="Purge">
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	(function($) {
		function setMessage(msg) {
			$('#fastcgi_purge_message').html('<p>' + msg + '</p>');
		}

		$('#purge_fastcgi').click(function(e) {
			e.preventDefault();
			var url = $('#purge_url').val();

			if( !(url == '' || url.match(/^\/[^\/]/)) ) { 
				setMessage('URL must be empty or start with one "/"');
				return;
			}
			
			setMessage('Sending purge request...');
			
			$.post("<?=$request_url?>", 
				{
					purge_url: url,
					XID: EE.XID
				}, function(data) {
					setMessage(data + ' URLs cleared from cache');
				}
			);
		});
	})(jQuery);
	
</script>