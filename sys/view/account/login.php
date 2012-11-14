<h2><?php echo Load::word('_global', 'login') ?></h2>
<section>
	<?php echo Form::open_form('login') ?>

		<ul>
			<?php echo Form::input(array(
						'label' => Load::word('account', 'email_address')
					,	'type' => 'email'
					,	'name' => 'email_address'
					,	'validate' => array('maxlength:255', 'minlength:1', 'regex:email')
					,	'autofocus' => 'autofocus'
				)); ?>

			<?php echo Form::input(array(
						'label' => Load::word('account', 'password')
					,	'type' => 'password'
					,	'name' => 'password'
					,	'validate' => array('minlength:6', 'regex:password')
				)); ?>

			<?php //echo Form::captcha(); ?>

		</ul>
		<footer>
			<button type="submit"><?php echo Load::word('_global', 'submit') ?></button>
		</footer>
	<?php echo Form::close_form(); ?>

</section>