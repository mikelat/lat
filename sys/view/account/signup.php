<h2>Sign up</h2>
<section>
	<form id="signup" method="post" action="<?php echo Url::make('account/signup') ?>">
		<ul>
			<li>
				<label class="required">Email Address</label>
				<input <?php //echo Form::input(array('type' => 'email', 'name' => 'email', 'validate' => array('required'))); ?> />
			</li>
			<li>
				<label class="required">Name</label>
				<input type="text" id="name" name="name" <?php //echo Form::validate(array('required', 'ajax')); ?> />
			</li>
			<li>
				<label class="required">Password</label>
				<input type="password" id="password" name="password" <?php //echo Form::validate(array('required', 'match:password')); ?> />
			</li>
			<li>
				<label class="required">Confirm Password</label>
				<input type="password" id="confirm_password" name="confirm_password" <?php // echo Form::validate(array('required', 'match:password')); ?> />
			</li>
			<li>
				<label class="required">Captcha</label>
				Coming soon
			</li>
		</ul>
		<footer>
			<button type="submit">Register</button>
		</footer>
	</form>
</section>