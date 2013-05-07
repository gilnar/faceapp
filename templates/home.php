<?php require_once 'header.php';?>

<div class="page-header">
	<h1>Welcome to Herolinks!</h1>
	<p align="right"><a class="btn large" href="/new" >Add a link now</a></p>
	
	<?php
	 	// We are not in a Facebook canvas but we are still logged and authorized
		if($facebookCanvas == FALSE && (!empty($fbUserProfile['email']))): ?>
		<p>You are currently logged into Facebook as <strong><?php echo $fbUserProfile['name'] ?></strong>. Not you? <a href="<?php echo $fbUserProfile['logout'] ?>">logout&raquo;</a></p>
	<?php endif; ?>
</div>

<?php if (!empty($flash['error'])): ?>
<div class="alert-message error">
	<?php echo $flash['error'] ?>
</div>
<?php endif; ?>

<?php if (!empty($flash['info'])): ?>
<div class="alert-message info">
	<?php echo $flash['info'] ?>
</div>
<?php endif; ?>

<?php if (!empty($links)): ?>
	<h2>Latest links</h2>

	<table class="linklist zebra-striped" summary="Latest submitted links">
		<tr>
			<th>Site</th>
			<th>Description</th>
			<th>User</th>
			<th>Date</th>
		</tr>
	<?php foreach($links as $link): ?>
		<tr>
			<td><a href="<?php echo $link['url'] ?>" rel="external"><?php echo $link['title'] ?></a></td>
			<td><?php echo $link['description'] ?></td>
			<td><?php echo $link['username'] ?></td>
			<td><?php echo date('d F Y H:i', strtotime($link['created'])) ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php else: ?>
	<div class="alert-message block-message info">
		<p>Sorry, the link database is empty!</p>
		<!-- <div class="alert-actions"><a class="btn primary" href="/new" >Add a link now</a></div> -->
	</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>