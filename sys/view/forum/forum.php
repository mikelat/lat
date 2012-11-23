		<li>
			<div class="cell">
				<h4><?php echo Url::make_slug('forum', $forum) ?></h4>
				<div class="statistics">
					<span class="threads"><?php echo Load::word('forum', 'stats_threads', String::number_format($forum['total_threads'])) ?></span>
					<span class="replies"><?php echo Load::word('forum', 'stats_replies', String::number_format($forum['total_replies'])) ?></span>
				</div>
				<div class="last-activity">
					<?php echo Url::make_slug('thread', $forum, 'last') ?><br />
					<?php echo Load::word('forum', 'stats_by', Url::make_slug('member', $forum, 'last')) ?>
					<?php echo String::time_format($forum['last_thread_time']) ?>
				</div>
				<a class="avatar">
					<img src="<?php echo Url::make('avatar.png', true) ?>" alt="" />
				</a>
				<div class="cb"></div>
			</div>
		</li>
