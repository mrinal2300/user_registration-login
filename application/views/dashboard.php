<ul class="nav nav-pills">
  <li role="presentation"><?php echo anchor('profile/view', 'Profile'); ?></li>
  <li role="presentation"><?php echo anchor('profile/change_password', 'Change password'); ?></li>
  <li role="presentation"><?php echo anchor('login/logout', 'Logout'); ?></li>
</ul>
<h3>Welcome <?php echo $user->first_name; ?></h3>
