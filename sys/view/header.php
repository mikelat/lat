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
<?php if(User::get('user_id')): ?>
					<div id="user-avatar">
						<img src="<?php echo Url::make('avatar.png', true) ?>" class="avatar" alt="" />
					</div>
<?php endif; ?>
					<form action="<?php echo Url::make('search') ?>" method="post" id="search">
						<input type="text" placeholder="<?php echo Load::word('_global', 'search') ?>" />
						<input type="hidden" name="section" id="search-section" value="" />
						<a href="#" id="search-button" class="btn c2"><?php echo Load::word('_global', 'go') ?></a>
					</form>
<?php if(!User::get('user_id')): ?>
					<a href="<?php echo Url::make('account/login') ?>" class="btn c1"><?php echo Load::word('_global', 'login') ?></a>
					<a href="<?php echo Url::make('account/signup') ?>" class="btn c1"><?php echo Load::word('_global', 'signup') ?></a>
<?php else: ?>
					<a href="<?php echo Url::make('account') ?>" id="account-button" class="btn c1"><?php echo User::get('display_name') ?></a>
<?php endif; ?>
				</li>
			</ul>
		</nav>
		<h1><a href="<?php echo Url::make() ?>">Lat Forum</a></h1>
		<div class="cb"></div>
	</section>
