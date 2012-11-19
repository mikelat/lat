		<li>
			<div class="cell">
				<h4><?php echo Model\Forum::link($forum) ?></h4>
				<div class="last-activity">
					Last post in <a href="#">Black Ops 2 Montage</a> by
					<a href="#">xXxSnipes420</a> <time>10 minutes ago</time>
				</div>
				<div class="statistics">
					<span class="threads">Threads: <?php echo String::number_format($forum['total_threads']) ?></span>
					<span class="replies">Replies: <?php echo String::number_format($forum['total_replies']) ?></span>
				</div>
				<div class="cb"></div>
			</div>
		</li>
