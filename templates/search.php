<?php require_once 'header.php'; ?>

<div class="page-header">
	<h1>Link Search</h1>

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

<?php if (!empty($links)): ?>

	<?php if (!empty($key)):?><h2>Search results for term '<?php echo $key ?>'</h2><?php endif; ?>

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
		<p>Sorry, no results found for term '<?php echo $key ?>'!</p>
	</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>