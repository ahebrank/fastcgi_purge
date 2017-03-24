<small style="position:relative; top: -3px;">Enter URL starting with slash (not http etc) or leave blank to purge everything.</small><br>
<div style="position:relative;">
<input id="purge_url" type="text" style="width:350px; margin:0 3px 0 0;">
<button id="purge_fastcgi" href="#" style="height:100%;position:absolute;">Purge</button>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function()
	{
		
		$('#purge_fastcgi').click(function(event)
		{
			event.preventDefault();
			
			var url = $('#purge_url').val(); 
			if( ! url.match(/^\/[^\/]/) ) { alert('URL must start with one "/"'); return; }
			
			$('#purge_url').val('Sending purge request...');
			
			$.post("<?=$request_url?>",
				{
					purge_url: url,
					XID: EE.XID
				}, function(data)
				{
					$('#purge_url').val('');
				}
			);
		});
	});
	
</script>