<!doctype html>
<html lang="en" class="<?php echo $classes ?>">
<head>
	<title>Lat CMS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADoAAAAyCAMAAAA6Njh3AAAAk1BMVEXhrmzhrmzhrmxmiJcxMTPcqmk1NTdggI41NTc1NTdkhZRmiJdmiJc1NTczMzU1NTfWpWZmiJdhgY9miJc1NTc1NTfhrmzhrmzhrmxmiJfhrmzTo2VmiJdmiJdmiJc1NTczMzVig5EyMjThrmzhrmzZp2jhrmw1NTdmiJc1NTcAAADTo2UxMTNff441NTfhrmxmiJem0u6JAAAAK3RSTlNwIDBQ7rNw7o9gs49woLMw26DbQCBQoI+AMFDuEGAgQMfH20AQx2AQgIAAYVxzHgAAAdJJREFUeNql1NeygjAUBdDYe0dBlCYtRIj8/9ddDklQaULufmAIsiZnZ1REWrKII9KcNmpRKku30jSk0vQsTyN5upWn9r+pY/SnlNE0XcvT5C5N07U8XcrTpCdV3jS996PWBz31oxHQsxTdArUYHfWjFBIyinvRBcgxkaE20I0MVSjkLEM3IGPC6aoHtShkLqjTgz5yqsBtktGkO434phAHtjW70nMu4zBfHIAaHekuFscLWQNdttCq3PDlNW2YGDXJRygezIDiDnRKS5KsgCb3X1Sxy1JMjH/QKGbSBlnEYW3bqPUAVv3PP+XUaaERhw+LlLJkIzfRkLeMF6QSM8mtUU9DPuw8JDUx0hqLvqStkPocaiz66DnOS3q66j9fkKevBh5hOTKLqzTOSk7hJpi8vuMzfefWMcuU0g2U1G6vap6aB5bPnOAS3e6yi85hFQfwDk5ZZidBRYb+qzm+C9/II8erL+pditf2qu4SiKtr++LpZQC/3YTZo/mm6CkcP1MRL/CLyp+Nr5y6t/dc1bgC33SY2uEW6KD4KIdt2B9mq9Esn5kgTxUwIM0J9qIy9MFQ2UCIPZoAbIvLz1GFhZlVxogEF1+Ds/kZT9cuqtjh5OA/WmDX+Ks6JVkAAAAASUVORK5CYII=" />
	<link rel="stylesheet/less" href="<?php echo Url::make('css/lat.less', true) ?>">
	<script>less = {env:'development'};</script>
	<script src="<?php echo Url::make('js/less.js', true) ?>"></script>
	<!-- IE not supported!! -->
	<!--[if lt IE9]><script>alert('go home and install chrome, i dont support filthy ie');window.location='https://www.google.com/chrome';</script><![endif]-->
</head>
<header>
	<nav id="nav-primary">
		<ul>
			<li><a href="<?php echo Url::make() ?>">Forums</a></li>
			<li><a href="<?php echo Url::make() ?>">Search</a></li>
			<li><a href="<?php echo Url::make() ?>">Members</a></li>
		</ul>
	</nav>
	<section>
		<nav id="nav-secondary">
			<ul>
				<li><a href="<?php echo Url::make('test') ?>">List</a></li>
				<li><a href="<?php echo Url::make() ?>">Statistics</a></li>
				<li><a href="<?php echo Url::make() ?>">Something</a></li>
				<li id="account">
					<!-- <div id="user-avatar">
						<img src="<?php echo Url::make('avatar.png', true) ?>" class="avatar" alt="" />
					</div> -->
					<form action="<?php echo Url::make('search') ?>" method="post" id="search">
						<input type="text" placeholder="<?php echo Load::word('_global', 'search') ?>" />
						<input type="hidden" name="section" id="search-section" value="" />
						<a href="#" id="search-button" class="btn c2"><?php echo Load::word('_global', 'go') ?></a>
					</form>
					<?php if(!Session::user('user_id')): ?>
						<a href="<?php echo Url::make('account') ?>" class="btn c1"><?php echo Load::word('_global', 'login') ?></a>
						<a href="<?php echo Url::make('account/signup') ?>" class="btn c1"><?php echo Load::word('_global', 'signup') ?></a>
					<?php else: ?>
						Logged in as: <?php echo Session::user('display_name') ?>
					<?php endif; ?>
				</li>
			</ul>
		</nav>
		<h1><a href="<?php echo Url::make() ?>">Lat Forum</a></h1>
		<div class="cb"></div>
	</section>
</header>
<section id="content">
<?php echo $html; ?>
</section>
<script>var lat = <?php echo json_encode(Load::javascript_var()) ?></script>
<script src="<?php echo Url::make('js/jquery.js', true) ?>"></script>
<script src="<?php echo Url::make('js/init.js', true) ?>"></script>
<?php if(Config::get('recaptcha_public')) { ?><script src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script><?php } ?>
<script src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<?php
	foreach(Load::javascript_file() as $jf) {
		echo '<script src="' . $jf . '"></script>';
	}
?>