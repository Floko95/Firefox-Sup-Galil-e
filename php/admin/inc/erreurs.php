<?php if(!empty($errors)): ?>
	<p class="red">
		<?php foreach ($errors as $error): ?>
			<?= $error; ?><br>
		<?php endforeach; ?>
	</p>
<?php endif; ?>
<?php if(!empty($success)): ?>
	<p class="green">
		<?php foreach ($success as $succes): ?>
			<?= $succes; ?><br>
		<?php endforeach; ?>
	</p>
<?php endif; ?>