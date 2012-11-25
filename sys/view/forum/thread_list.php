<h2><?php echo $forum['name']?></h2>
<section>
<?php if(!empty($forum_list)): ?>
	<h3><?php echo Load::word('forum', 'sub-forums') ?></h3>
	<ul class="forum-list content-list">
<?php foreach($forum_list as $forum): ?>
<?php Load::view('forum/forum', array('forum' => $forum)) ?>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endif; ?>
<?php if(!empty($thread_list)): ?>
	<h3><?php echo Load::word('forum', 'threads') ?></h3>
	<ul class="thread-list content-list">
<?php foreach($thread_list as $thread): ?>
		<li>
			<div class="cell">
				<div class="thread-title"><?php echo Url::make_slug('thread', $thread) ?></div>
				<div class="started-by"><?php echo Load::word('forum', 'created_by', String::time_format($thread['start_time']), Url::make_slug('member', $thread, 'start')) ?></div>
				<div class="statistics">
					<span class="replies"><?php echo Load::word('forum', 'stats_replies', String::number_format($thread['total_replies'])) ?></span>
					<span class="views"><?php echo Load::word('forum', 'stats_views', String::number_format($thread['total_views'])) ?></span>
				</div>
				<a class="avatar">
					<img src="<?php echo Url::make('avatar.png', true) ?>" alt="" />
				</a>
				<div class="last-activity">
					<?php echo Load::word('forum', 'stats_by', Url::make_slug('member', $thread, 'last')) ?>
					<?php echo String::time_format($thread['last_reply_time']) ?>
				</div>
				<div class="cb"></div>
			</div>
		</li>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endif; ?>
</section>