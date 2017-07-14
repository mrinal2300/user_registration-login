<div class="col-md-6 modal-body">
<?php echo form_open('login'); ?>
<?php echo $this->session->flashdata('error_message'); ?>

<h5>Email Address</h5>
<input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"/>
<?php echo form_error('email'); ?>

<h5>Password</h5>
<input type="password" class="form-control" name="password"/>
<?php echo form_error('password'); ?>
</br>
<?php if($g_recaptcha){ ?>
<div class="g-recaptcha" data-sitekey="6LcjxiUUAAAAAPhokNcsUtWXfXz0XGw0kPhOUY2i"></div>
<?php echo form_error('g-recaptcha-response'); ?>
<?php }?>

<div><input type="submit" class="btn btn-primary" value="Login" /></div>

</form>
<?php echo anchor('register', 'Register')."\n"; ?>
<?php echo anchor('forgot', 'Forget password'); ?>
</div>