<h2><?php echo Load::word('_global', 'login') ?></h2>
<section>
	<?php echo Form::open_form('login') ?>

		<ul>
			<?php echo Form::input(array(
						'label' => Load::word('account', 'name')
					,	'name' => 'name'
					,	'validate' => array('maxlength:u15', 'minlength:2')
				)); ?>

			<?php echo Form::input(array(
						'label' => Load::word('account', 'password')
					,	'type' => 'password'
					,	'name' => 'password'
					,	'validate' => array('minlength:6', 'regex:password')
				)); ?>

			<?php echo Form::checkbox('remember_me', Load::word('account', 'remember_me')); ?>

			<?php echo Form::captcha((User::lock() < 100)); ?>

			<li class="max marginl">
				<a class="btn c2" href="<?php echo Url::make('/account/recover') ?>"><?php echo Load::word('account', 'recover_password') ?></a>
			</li>

		</ul>
		<footer>
			<button type="submit"><?php echo Load::word('_global', 'submit') ?></button>
		</footer>
	<?php echo Form::close_form(); ?>

</section>