<?php $out = <<<HTML
<div id="debug">
	<b>Version:</b> <!-- var:version --><br />
	<b>Queries Executed:</b> <!-- var:queries --><br />
	<b>Query Time:</b> <!-- var:query-time --><br />
	<b>Exec Time:</b> <!-- var:exec-time --><br />
	<a href="#" onclick="$('#debug_data').toggle();">toggle debug info</a>
</div>
<div id="debug_data">
	<!-- var:log -->
</div>

HTML;
