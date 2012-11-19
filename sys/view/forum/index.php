<h2>Forum List</h2>
<section>
<?php foreach ($forums[0] as $root_forum): ?>
	<h3><?php echo Model\Forum::link($root_forum) ?></h3>
	<ul class="big">
<?php foreach($forums[$root_forum['forum_id']] as $forum): ?>
<?php Load::view('forum/forum', array('forum' => $forum)) ?>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endforeach; ?>
</section>