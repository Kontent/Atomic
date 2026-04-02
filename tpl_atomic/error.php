<?php
/**
 * @copyright	Copyright (C) 2008-2026 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if (!isset($this->error)) {
	$this->error = new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
	$this->debug = false;
}

$app  = Factory::getApplication();
$root = Uri::root(true);
$code = $this->error->getCode();

// Template params
$bootstrapsource  = $this->params->get('bootstrapsource', 2);
$bootstrapcdn     = $this->params->get('bootstrapcdn');
$bsfixjoomla      = $this->params->get('bsfixjoomla', 1);
$atomicstyles     = $this->params->get('atomicstyles', 0);
$errorsearch      = (int) $this->params->get('errorsearch', 1);
$logo             = $this->params->get('logo');
$sitetitle        = $this->params->get('sitetitle');
$headerfont       = $this->params->get('headerfont');
$headerfontname   = $this->params->get('headerfontname');
$bodyfont         = $this->params->get('bodyfont');
$bodyfontname     = $this->params->get('bodyfontname');
$systemFontHeader = $this->params->get('systemFontHeader', '');
$systemFontBody   = $this->params->get('systemFontBody', '');

$headerfontfamily = getGoogleFontFamily($headerfont, 'header', $headerfont == 13 ? $systemFontHeader : $headerfontname);
$bodyfontfamily   = getGoogleFontFamily($bodyfont, 'body', $bodyfont == 13 ? $systemFontBody : $bodyfontname);
$isheadergooglefont = isGoogleFont($headerfont);
$isbodygooglefont   = isGoogleFont($bodyfont);

$bstheme        = $this->params->get('bstheme', '');
$bsthemecustom  = trim((string) $this->params->get('bsthemecustom', ''));
if ($bstheme === 'custom') {
	$bstheme = $bsthemecustom !== '' ? strtolower($bsthemecustom) : '';
} elseif (!in_array($bstheme, ['light', 'dark', 'auto'])) {
	$bstheme = '';
} else {
	$bstheme = strtolower($bstheme);
}
$bsthemeInitial = $bstheme;
if ($bsthemeInitial === 'auto') {
	$bsthemeInitial = 'light';
}

// Determine error title and description
if ($code === 403) {
	$errorTitle = Text::_('TPL_ATOMIC_ERROR_403_TITLE');
	$errorDesc  = Text::_('TPL_ATOMIC_ERROR_403_DESC');
} elseif ($code === 404) {
	$errorTitle = Text::_('TPL_ATOMIC_ERROR_404_TITLE');
	$errorDesc  = Text::_('TPL_ATOMIC_ERROR_404_DESC');
} else {
	$errorTitle = $this->title;
	$errorDesc  = htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $bsthemeInitial !== '' ? ' data-bs-theme="' . htmlspecialchars($bsthemeInitial, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex">
		<title><?php echo $code; ?> - <?php echo $this->title; ?></title>

		<?php if ($bstheme !== '') : ?>
		<script>(function(){var d=document.documentElement,s=localStorage.getItem('theme'),t=s||'<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}d.setAttribute('data-bs-theme',t);})()</script>
		<?php endif; ?>

		<?php
		// Google Fonts
		if ($isheadergooglefont || $isbodygooglefont) {
			echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
			echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
			$fontsToLoad = array_unique([$headerfont, $bodyfont]);
			foreach ($fontsToLoad as $font) {
				if ($font) {
					echo getGoogleFontLink($font);
				}
			}
		}
		?>

		<?php // Bootstrap CSS
			if($bootstrapsource == 1 || $bootstrapsource == 3 || $bootstrapsource == 4) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/vendor/bootstrap/css/bootstrap.min.css">
			<?php elseif($bootstrapsource == 2) : ?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 5) : ?>
				<?php echo $bootstrapcdn; ?>
			<?php elseif($bootstrapsource == 6) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/cosmo/bootstrap.min.css" integrity="sha384-QOrayDhdkHbTAsh/gb0iGlDY/xHwI3sdDvyHkxnfpY20Y+Pa8aRHFXmLQYklmIx/" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 7) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/flatly/bootstrap.min.css" integrity="sha384-MZ3pnZEBOL1wAG2nrP+M1A9LCApF229c39UC+1T3B96aI3VjuAWMeb1I99GMJacE" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 8) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/minty/bootstrap.min.css" integrity="sha384-7cNn55KSVPdYzfuegchZCGqbVrV6ksXrmgEb1VZbPHSwQqCFDFTrQfg8MsZmSI7u" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 9) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/spacelab/bootstrap.min.css" integrity="sha384-6O06/mG6zTPV5qcszBfW91idf95OvvBVrsSlQ23AP7bq5TYK0Gh4lmmHSf47i/B2" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 10) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css" integrity="sha384-OMwG/TAHy7NRQbZ6SZ/45S4g8n76iLIAkbYP8evydAdSZiO97yIyh5g2zThHlY8r" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 11) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/cyborg/bootstrap.min.css" integrity="sha384-qxGSw6SRX7woR/PK/wbYrdowFJ2DdFQF+nwWswHGKp+jqtYAQKCvBzB/3b+Pjx6W" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 12) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/darkly/bootstrap.min.css" integrity="sha384-t2UKecXY6tDoQIsEiNhYTaTFWmoHgQT7MV80h9huTejPYLkdgaOHv8ssDrS3Cdcw" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 13) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/slate/bootstrap.min.css" integrity="sha384-qhrBcipvKS9sQcI3lcoXpdKNs9jmQAunazzwZW3aZeuFMoRih2NYJIDsr6XTFndn" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 14) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/superhero/bootstrap.min.css" integrity="sha384-fU437/gCPFVIYQG5/RnXUGGh+prGONHn6C9GslvteaFVmNCeul6aHunWDz85bM78" crossorigin="anonymous">
		<?php endif; ?>

		<?php // Font CSS custom properties
			$rootParts = [];
			if ($headerfont != 2 && !empty($headerfontfamily)) {
				$rootParts[] = '--atomic-header-font: ' . $headerfontfamily;
			}
			if ($bodyfont != 2 && !empty($bodyfontfamily)) {
				$rootParts[] = '--atomic-body-font: ' . ($bodyfont != 0 ? $bodyfontfamily : 'var(--bs-body-font-family)');
			}
			if (!empty($rootParts)) {
				echo '<style>:root {' . implode(';', $rootParts) . ';}</style>';
			}
		?>

		<?php // Atomic CSS
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/atomic.min.css" type="text/css">
		<?php endif; ?>

		<?php // Atomic styles
			if($atomicstyles == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/atomicstyles.min.css" type="text/css">
		<?php endif; ?>

		<style>
			.error-page {
				min-height: 100vh;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				padding: 2rem 1rem;
				text-align: center;
			}
			.error-code {
				font-size: clamp(5rem, 20vw, 9rem);
				font-weight: 900;
				letter-spacing: -0.04em;
				line-height: 1;
				margin-bottom: 0.5rem;
			}
			.error-title {
				font-size: clamp(1.25rem, 4vw, 1.75rem);
				font-weight: 600;
				margin-bottom: 1rem;
			}
			.error-desc {
				max-width: 480px;
				margin: 0 auto 2rem;
				line-height: 1.6;
			}
			.error-search {
				max-width: 420px;
				width: 100%;
				margin: 0 auto 2rem;
			}
			.error-logo {
				max-height: 60px;
				width: auto;
				margin-bottom: 1.5rem;
			}
			.error-debug {
				text-align: left;
				max-width: 800px;
				overflow: auto;
				font-family: monospace;
				font-size: 0.75rem;
			}
		</style>
	</head>
	<body class="error-page">

		<?php // Logo ?>
		<?php if (!empty($logo)) : ?>
			<img src="<?php echo $this->baseurl . '/' . htmlspecialchars($logo) ?>"
				 alt="<?php echo htmlspecialchars($sitetitle ?: '') ?>"
				 class="error-logo" loading="eager" decoding="async" />
		<?php endif; ?>

		<div class="error-code text-body-emphasis"><?php echo $code; ?></div>
		<h1 class="error-title"><?php echo $errorTitle; ?></h1>
		<p class="error-desc text-body-secondary"><?php echo $errorDesc; ?></p>

		<?php // Error-specific module position ?>
		<?php if ($code === 403) : ?>
			<jdoc:include type="modules" name="error-403" style="none" />
		<?php elseif ($code === 404) : ?>
			<jdoc:include type="modules" name="error-404" style="none" />
		<?php endif; ?>

		<?php if ($errorsearch) : ?>
		<div class="error-search">
			<form action="<?php echo $this->baseurl; ?>/index.php" method="get" role="search" class="d-flex gap-2">
				<input type="hidden" name="option" value="com_finder">
				<input type="hidden" name="view" value="search">
				<input type="search" name="q" class="form-control" placeholder="<?php echo Text::_('TPL_ATOMIC_ERROR_SEARCH_PLACEHOLDER'); ?>" aria-label="<?php echo Text::_('TPL_ATOMIC_ERROR_SEARCH_LABEL'); ?>">
				<button type="submit" class="btn btn-primary text-nowrap"><?php echo Text::_('TPL_ATOMIC_ERROR_SEARCH_BUTTON'); ?></button>
			</form>
		</div>
		<?php endif; ?>

		<nav class="d-flex flex-wrap gap-2 justify-content-center mb-4" aria-label="<?php echo Text::_('TPL_ATOMIC_ERROR_NAV_LABEL'); ?>">
			<a href="<?php echo $this->baseurl; ?>/index.php" class="btn btn-outline-secondary"><?php echo Text::_('TPL_ATOMIC_ERROR_HOME'); ?></a>
			<a href="#" onclick="history.length > 1 ? history.back() : window.location.href='<?php echo $this->baseurl; ?>/index.php'; return false;" class="btn btn-outline-secondary"><?php echo Text::_('TPL_ATOMIC_ERROR_GOBACK'); ?></a>
		</nav>

		<?php if ($this->debug) : ?>
			<div class="error-debug bg-body-tertiary p-3 rounded mb-3">
				<?php echo $this->renderBacktrace(); ?>
			</div>
		<?php endif; ?>

	</body>
</html>
