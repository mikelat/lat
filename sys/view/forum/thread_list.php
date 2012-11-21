<h2><?php echo $forum['name']?></h2>
<section>
<?php if(!empty($forum_list)): ?>
	<h3><?php echo Load::word('forum', 'sub-forums') ?></h3>
	<ul class="forum-list">
<?php foreach($forum_list as $forum): ?>
<?php Load::view('forum/forum', array('forum' => $forum)) ?>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endif; ?>
<?php if(!empty($thread_list)): ?>
	<h3><?php echo Load::word('forum', 'threads') ?></h3>
	<ul class="thread-list">
<?php foreach($thread_list as $thread): ?>
		<li>
			<div class="cell">
				<strong><?php echo Url::make_slug('thread', $thread) ?> </strong>
			</div>
		</li>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endif; ?>
</section>