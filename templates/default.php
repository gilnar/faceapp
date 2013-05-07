<?php require_once 'header.php';?>

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

<?php require_once 'footer.php'; ?>