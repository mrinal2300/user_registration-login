<div class="col-md-6">
<?php echo form_open('forgot/reset_password/'.$token); ?>

<h3>Enter new password</h3>

<h5>Password</h5>
<input type="password" class="form-control" name="password"/>
<?php echo form_error('password'); ?>


<h5>Password Confirm</h5>
<input type="password" class="form-control" name="passconf"/>
<?php echo form_error('passconf'); ?>
</br>
<div><input type="submit" class="btn btn-primary" name="submit" value="Reset Password" /></div>
</form>
</div>