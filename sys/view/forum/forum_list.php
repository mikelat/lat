<?php foreach ($forum_list[0] as $root_forum): ?>
	<h3><?php echo isset($root_forum['slug']) ? Url::make_slug('forum', $root_forum) : $root_forum['name'] ?></h3>
	<ul class="forum-list content-list">
<?php foreach($forum_list[$root_forum['forum_id']] as $forum): ?>
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
				<?php echo Url::make_avatar($forum, 'last') ?>
				<div class="cb"></div>
			</div>
		</li>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endforeach; ?>