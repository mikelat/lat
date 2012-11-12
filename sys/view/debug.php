<div class="tc">
<strong>Coded By:</strong> Mike Lat
<strong class="pl">Version:</strong> <?php echo $version; ?>
<strong class="pl">Queries Executed:</strong> <?php echo $queries; ?>
<strong class="pl">Query Time:</strong> <?php echo $query_time; ?>
<strong class="pl">Exec Time:</strong> <?php echo $exec_time; ?>
<strong class="pl"><a href="<?php echo Url::make('forum/cache') ?>">force cache reload</a></strong>
<strong class="pl"><a href="#" onclick="var	lr = 0; setInterval(function(){ lr = (lr + 1) % 360; $('html').css({ WebkitTransform: 'rotate(' + lr + 'deg)', '-moz-transform': 'rotate(' + lr + 'deg)'});	}, 1); return false;">spin the page</a></strong>
</div>
<div id="debug">
	<a href="#" onclick="$('#debug_data').toggle(); return false;">debug</a>
	<span id="in_desktop">[desktop]</span>
	<span id="in_tablet">[tablet]</span>
	<span id="in_mobile">[mobile]</span>
</div>
<ul id="debug_data">
	<li id="debug_head">
		Debug Summary
		<a href="#" onclick="$('#debug_data').toggle(); return false;">close debug summary</a>
		<a href="#" onclick="$('.debug-debug').toggleClass('on'); return false;" class="debug-debug">toggle debug info</a>
	</li>
	<?php foreach ($log as $l) {
		echo '<li class="debug-' . $l[0] . '"><strong>[' . strtoupper($l[0]) . ']</strong> '
	 	. $l[1] . ($l[2] > 0 ? ' <em>(executed in '.number_format($l[2], 6). 's)</em></li>' : '</li>');
	} ?>

</ul>