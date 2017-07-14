<div class="col-md-6">
<?php echo form_open('register'); ?>

<h5>First name</h5>
<input type="text" class="form-control" name="first_name" value="<?php echo set_value('first_name'); ?>"/>
<?php echo form_error('first_name'); ?>

<h5>Last name</h5>
<input type="text" class="form-control" name="last_name" value="<?php echo set_value('last_name'); ?>"/>
<?php echo form_error('last_name'); ?>


<h5>Password</h5>
<input type="password" class="form-control" name="password"/>
<?php echo form_error('password'); ?>


<h5>Password Confirm</h5>
<input type="password" class="form-control" name="passconf"/>
<?php echo form_error('passconf'); ?>


<h5>Email Address</h5>
<input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"/>
<?php echo form_error('email'); ?>
</br>
<div><input type="submit" class="btn btn-primary" value="Submit" /></div>

</form>
<?php echo anchor('login', 'Already register login here'); ?>
</div>