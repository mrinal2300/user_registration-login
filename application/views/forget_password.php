<div class="col-md-6">
<?php echo form_open('forgot'); ?>

<h5>Enter your email to reset your password</h5>
<input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"/>
<?php echo form_error('email'); ?>
</br>

<div><input type="submit" class="btn btn-primary" name="submit" value="Reset" /></div>

</form>
</div>