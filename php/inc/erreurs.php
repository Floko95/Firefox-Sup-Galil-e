<?php if(!empty($errors)): ?>
	<div class="alert" id="alert">
		<div class="alert-content"  id="alert-content">
			<div class="alert-header rouge">Echec :</div>
			<div class="alert-body">
				<?php foreach ($errors as $error): ?>
					<?= $error; ?><br>
				<?php endforeach; ?>
			</div>
			<div class="alert-footer"><img src="../../img/cancel.png"></div>
		</div>
	</div>
<?php endif; ?>

<?php if(!empty($success)): ?>
	<div class="alert" id="alert">
		<div class="alert-content"  id="alert-content">
			<div class="alert-header vert">Succès :</div>
			<div class="alert-body">
				<?php foreach ($success as $succes): ?>
					<?= $succes; ?><br>
				<?php endforeach; ?>
			</div>
			<div class="alert-footer"><img src="../../img/cancel.png"></div>
		</div>
	</div>
<?php endif; ?>

<?php if (isset($_SESSION['flash'])): ?>
	<div class="alert" id="alert">
		<div class="alert-content"  id="alert-content">
			<div class="alert-header orange">Information :</div>
			<div class="alert-body">
				<?php foreach($_SESSION['flash'] as $type => $message): ?>
					<?= $message; ?>
				<?php endforeach; ?>
			</div>
			<div class="alert-footer"><img src="../img/cancel.png"></div>
		</div>
	</div>
	<?php unset($_SESSION['flash']); ?>
<?php endif; ?>