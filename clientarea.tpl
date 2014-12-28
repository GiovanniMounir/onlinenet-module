{literal}
    <link rel="stylesheet" type="text/css" media="all" href="modules/servers/online/css/online.css">
{/literal}
<h1 id="cntrlhead">Control Panel</h1>
<hr>
<ul class="nav nav-pills nav-stacked">
              <li{php} if ($_GET['b'] == 'state' || empty($_GET['b'])) { echo ' class="active"'; } {/php}><a href="clientarea.php?action=productdetails&id={$srvid}&b=state">State</a></li>
          	  <li{php} if ($_GET['b'] == 'remote') { echo ' class="active"'; } {/php}><a href="clientarea.php?action=productdetails&id={$srvid}&b=remote">Remote Console</a></li>
			  <li{php} if ($_GET['b'] == 'network') { echo ' class="active"'; } {/php}><a href="clientarea.php?action=productdetails&id={$srvid}&b=network">Network</a></li>
			  <li{php} if ($_GET['b'] == 'raid') { echo ' class="active"'; } {/php}><a href="clientarea.php?action=productdetails&id={$srvid}&b=raid">RAID</a></li>
			  
            </ul>
			<div class="ccontent">
			{$message}
			{php}
			
			if ($_GET['b'] == "state")
			{
		    	if ($_GET['c'] == "hostname")
		    	{
				{/php}
			        {$hostname}
			    {php}
				}
				else if ($_GET['c'] == "rescue")
		    	{
				{/php}
			        {$rescue}
			    {php}
				}
			{/php}
			{$state}
            {php}
			}
			else if ($_GET['b'] == "network") 
			{
			{/php}
			{$network}
            {php}
			}
			else if ($_GET['b'] == "raid") 
			{
				{/php}
			        {$raid}
			    {php}
			}
			else if ($_GET['b'] == 'remote')
			{
				{/php}
			        {$remote}
			    {php}
				
			}
			
			else
			{
			{/php}
			{$state}
            {php}
			}
			{/php}
			</div>
			{literal}
			<script>
			$(document).ready(function () {
                $(window).scrollTop($('#cntrlhead').offset().top);
				});
				$(function(){   
   $(".alert-message").delegate("a.close", "click", function(event) {
        event.preventDefault();
        $(this).closest(".alert-message").fadeOut(function(event){
            $(this).remove();
        });
    });
   });
   var fade_out = function() {
  $(".alert-message").fadeOut().empty();
}

setTimeout(fade_out, 5000);
			</script>
			{/literal}
