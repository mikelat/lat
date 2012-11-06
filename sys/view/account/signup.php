<h2>Sign up</h2>
<section>
	<?php echo Form::open_form('signup') ?>

		<ul>
			<?php echo Form::input(array(
						'label' => 'Email Address'
					,	'type' => 'email'
					,	'name' => 'email_address'
					,	'validate' => array('minlength:1', 'regex:email')
					,	'autofocus' => 'autofocus'
				)); ?>

			<?php echo Form::input(array(
						'label' => 'Display Name'
					,	'name' => 'display_name'
					,	'validate' => array('minlength:2', 'ajax')
				)); ?>

			<?php echo Form::input(array(
						'label' => 'Password'
					,	'type' => 'password'
					,	'name' => 'password'
					,	'validate' => array('minlength:6')
				)); ?>

			<?php echo Form::input(array(
						'label' => 'Confirm Password'
					,	'type' => 'password'
					,	'name' => 'confirm_password'
					,	'validate' => array('minlength:6', 'match:password')
				)); ?>

			<li>
				<label class="required">Captcha</label>
				Coming soon
			</li>
		</ul>
		<footer>
			<button type="submit">Register</button>
		</footer>
	<?php echo Form::close_form(); ?>

</section>