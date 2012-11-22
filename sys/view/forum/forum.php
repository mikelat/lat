		<li>
			<div class="cell">
				<h4><?php echo Url::make_slug('forum', $forum) ?></h4>
				<div class="last-activity">
					<?php echo Url::make_slug('thread', $forum, 'last') ?>
					<time>10 minutes ago</time>
					by <?php echo Url::make_slug('member', $forum, 'last') ?>
				</div>
				<a class="avatar">
					<img src="<?php echo Url::make('avatar.png', true) ?>" alt="" />
				</a>
				<div class="statistics">
					<span class="threads">Threads: <?php echo String::number_format($forum['total_threads']) ?></span>
					<span class="replies">Replies: <?php echo String::number_format($forum['total_replies']) ?></span>
				</div>
				<div class="cb"></div>
			</div>
		</li>
