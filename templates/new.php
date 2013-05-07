<?php require_once 'header.php'; ?>

<div class="page-header">
	<h1>Add a new link</h1>

</div>

<?php if (!empty($flash['info'])): ?>
<div class="alert-message info">
	<?php echo $flash['info'] ?>
</div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
<div class="alert-message error">
	<?php echo $flash['error'] ?>
</div>
<?php endif; ?>

<form action="" method="post" accept-charset="utf-8">
	
	<fieldset>
		
	<div class="clearfix<?php if (!empty($errors['url'])) echo ' error' ?>">
		<label for="url">Site URL</label>
		<div class="input">
			<input type="text" size="30" name="url" id="url" class="xlarge" value="<?php echo (!empty($data['url'])) ? $data['url'] : ''; ?>">
			<?php $field = 'url'; if (!empty($errors[$field])):?><span class="help-inline"><?php echo $errors[$field] ?></span><?php endif; ?>
		</div>
	</div>	

	<div class="clearfix<?php if (!empty($errors['title'])) echo ' error' ?>">
		<label for="title">Title</label>
		<div class="input">
			<input type="text" size="30" name="title" id="title" class="xlarge" value="<?php echo (!empty($data['title'])) ? $data['title'] : ''; ?>">
			<?php $field = 'title'; if (!empty($errors[$field])):?><span class="help-inline"><?php echo $errors[$field] ?></span><?php endif; ?>
		</div>
	</div>	

	<div class="clearfix">
		<label for="description">Description</label>
		<div class="input">
			<textarea rows="3" name="description" id="description" class="xxlarge"><?php echo (!empty($data['description'])) ? $data['description'] : ''; ?></textarea>
			<span class="help-block">An optional comment for your friends out there...</span>
		</div>
	</div>	

	<div class="clearfix<?php if (!empty($errors['username'])) echo ' error' ?>">
		<label for="username">Your Name</label>
		<div class="input">
			<?php if (!empty($fbUserProfile['name'])): ?>
				<input type="hidden" name="username" id="username" value="<?php echo (!empty($data['username'])) ? $data['username'] : ''; ?>">
				<span class="fb-profile fb-name"><?php echo $fbUserProfile['name'];?></span>
			<?php else: ?>
			<input type="text" size="30" name="username" id="username" class="xlarge" value="<?php echo (!empty($data['username'])) ? $data['username'] : ''; ?>">
			<?php $field = 'username'; if (!empty($errors[$field])):?><span class="help-inline"><?php echo $errors[$field] ?></span><?php endif; ?>
			<?php endif; ?>
		</div>
	</div>	

	<div class="clearfix<?php if (!empty($errors['useremail'])) echo ' error' ?>">
		<label for="useremail">Your Email (private)</label>
		<div class="input">
			<?php if (!empty($fbUserProfile['email'])): ?>
				<input type="hidden" name="useremail" id="useremail" value="<?php echo (!empty($data['useremail'])) ? $data['useremail'] : ''; ?>">
				<span class="fb-profile fb-email"><?php echo $fbUserProfile['email'];?></span>
			<?php else: ?>
			<input type="text" size="30" name="useremail" id="useremail" class="xlarge" value="<?php echo (!empty($data['useremail'])) ? $data['useremail'] : ''; ?>">
			<?php $field = 'useremail'; if (!empty($errors[$field])):?><span class="help-inline"><?php echo $errors[$field] ?></span><?php endif; ?>
			<?php endif; ?>
		</div>
	</div>	

	<div class="actions">
		<input type="submit" value="Share it!" class="btn primary">&nbsp;<a class="btn" href="/" >No, thanks</a>
	</div>
	</fieldset>
</form>

<?php require_once 'footer.php'; ?>