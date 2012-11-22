<h2>Forum List</h2>
<section>
<?php foreach ($forum_list[0] as $root_forum): ?>
	<h3><?php echo Url::make_slug('forum', $root_forum) ?></h3>
	<ul class="forum-list content-list">
<?php foreach($forum_list[$root_forum['forum_id']] as $forum): ?>
<?php Load::view('forum/forum', array('forum' => $forum)) ?>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endforeach; ?>
</section>