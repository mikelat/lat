<h2><?php echo $forum['name']?></h2>
<section>
<?php if(!empty($forum_list)): ?>
	<ul class="big">
<?php foreach($forum_list as $forum): ?>
<?php Load::view('forum/forum', array('forum' => $forum)) ?>
<?php endforeach; ?>
	</ul>
	<div class="cb"></div>
<?php endif; ?>
	<h3>Threads</h3>
</section>