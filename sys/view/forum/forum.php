		<li>
			<div class="cell">
				<h4><?php echo Url::make_slug('forum', $forum) ?></h4>
				<div class="last-activity">
					Last post in <?php echo Url::make_slug('thread', $forum, 'last') ?> by<br />
					<?php echo Url::make_slug('member', $forum, 'last') ?> <time>10 minutes ago</time>
				</div>
				<div class="statistics">
					<span class="threads">Threads: <?php echo String::number_format($forum['total_threads']) ?></span>
					<span class="replies">Replies: <?php echo String::number_format($forum['total_replies']) ?></span>
				</div>
				<div class="cb"></div>
			</div>
		</li>
