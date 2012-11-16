<h2><?php echo Load::word('_global', 'signup') ?></h2>
<section>
	<?php echo Form::open_form('signup') ?>

		<ul>
			<?php echo Form::input(array(
						'label' => Load::word('account', 'display_name')
					,	'name' => 'display_name'
					,	'validate' => array('maxlength:25', 'minlength:2', 'ajax')
				)); ?>

			<?php echo Form::input(array(
						'label' => Load::word('account', 'password')
					,	'type' => 'password'
					,	'name' => 'password'
					,	'validate' => array('minlength:6', 'regex:password')
				)); ?>

			<?php echo Form::input(array(
						'label' => Load::word('account', 'confirm_password')
					,	'type' => 'password'
					,	'name' => 'confirm_password'
					,	'validate' => array('minlength:6', 'match:password')
				)); ?>

			<?php echo Form::captcha(); ?>

		</ul>
		<footer>
			<button type="submit"><?php echo Load::word('_global', 'submit') ?></button>
		</footer>
	<?php echo Form::close_form(); ?>

</section>