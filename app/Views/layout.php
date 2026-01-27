<!doctype html>
<?php
// Get theme mode from helper function
$themeMode = get_theme_mode();
?>
<html lang="en" data-theme-mode="<?= esc($themeMode) ?>">
	<head>
		<meta charset="utf-8">
		<title><?= esc($title ?? 'Threadline') ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
		<link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
		<?= $this->renderSection('styles') ?>
		
		<!-- Theme initialization script (prevents FOUC) -->
		<script>
		(function() {
			var themeMode = document.documentElement.getAttribute('data-theme-mode') || 'auto';
			var resolvedTheme = 'light';
			
			if (themeMode === 'dark') {
				resolvedTheme = 'dark';
			} else if (themeMode === 'light') {
				resolvedTheme = 'light';
			} else {
				// auto mode - check system preference
				if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
					resolvedTheme = 'dark';
				}
			}
			
			document.documentElement.setAttribute('data-bs-theme', resolvedTheme);
		})();
		</script>
	</head>
	<body>

		<?= $this->include('partials/header') ?>

		<main class="<?= (isset($noContainer) && $noContainer) ? 'home-main' : 'container my-4' ?>">
			<?= $this->renderSection('content') ?>
		</main>

		<script src="<?= base_url('assets/theme.js') ?>"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
		<script src="<?= base_url('assets/app.js') ?>"></script>
		<?= $this->renderSection('scripts') ?>
	</body>
</html>
