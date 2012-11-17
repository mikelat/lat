<h2>Forum List</h2>
<section>
<?php foreach ($forums_parent[0] as $root_forum): ?>
	<h3><?php echo Model\Forum::link($root_forum['name'], $root_forum['forum_id'], $root_forum['slug']) ?></h3>
	<ul class="big">
<?php foreach($forums_parent[$root_forum['forum_id']] as $forum): ?>
		<li>
			<h4><?php echo Model\Forum::link($forum['name'], $forum['forum_id'], $forum['slug']) ?></h4>
			<div class="last-activity">
				Last post in <a href="#">Black Ops 2 Montage</a> by
				<a href="#">xXxSnipes420</a> <time>10 minutes ago</time>
			</div>
			<div class="statistics">
				<span class="threads">Threads: 9000</span>
				<span class="replies">Replies: 9000</span>
			</div>
			<div class="cb"></div>
		</li>
<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
</section>