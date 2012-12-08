<h2>zomg thread</h2>
<section>
	<ul class="reply-list content-list">
<?php $reply_number = 0;?>
<?php foreach($replies as $reply): ?><?php $reply_number++; ?>
		<li>
			<div class="cell">
				<h3>
					<?php echo Url::make_slug('member', $reply) ?>
					<?php echo String::time_format($reply['reply_created'], true) ?>
					<a class="reply-link" href="#"><?php echo 'Reply #' . $reply_number ?></a>
				</h3>
				<?php echo Url::make_avatar($reply) ?>
				<?php echo $reply['content_cached'] ?>
				<div class="cb"></div>
			</div>
		</li>
<?php endforeach; ?>
	</ul>
</section>