<!doctype html>
<html lang="en">
	<head>
			<meta charset="utf-8">
			<title><?= esc($title ?? 'Forum') ?></title>
			<meta name="viewport" content="width=device-width, initial-scale=1">

			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
			<link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
	</head>
	<body class="bg-light">

		<?= $this->include('partials/header') ?>

		<main class="container my-4">
				<?= $this->renderSection('content') ?>
		</main>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script src="<?= base_url('assets/app.js') ?>"></script>
		<?= $this->renderSection('scripts') ?>
	</body>
</html>
