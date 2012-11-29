<h2>zomg thread</h2>
<section>
	<ul class="reply-list content-list">
<?php $reply_number = 0;?>
<?php foreach($replies as $reply): ?><?php $reply_number++; ?>
		<li>
			<h3>
				<?php echo Url::make_slug('member', $reply) ?>
				<?php echo String::time_format($reply['reply_created'], true) ?>
				<a class="reply-link" href="#"><?php echo 'Reply #' . $reply_number ?></a>
			</h3>
			<div class="cell">
				<aside>
					<?php echo Url::make_avatar($reply) ?>
					<div class="user-statistics">
						Total Posts: <?php echo $reply['member_forum_posts'] ?>
					</div>
				</aside>
				<?php echo $reply['content_cached'] ?>
				<div class="cb"></div>
			</div>
			<footer>

			</footer>
		</li>
<?php endforeach; ?>
	</ul>
</section>