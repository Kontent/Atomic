<?php
/**
 * @copyright	Copyright (C) 2008-2026 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\UserGroupsHelper;

$app   = Factory::getApplication();
$input = $app->getInput();
$wa    = $this->getWebAssetManager();

//	Get user group for attribute

function sanitizeForDataAttrComp($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

$user     = $app->getIdentity();
$dataUser = '';

if (!$user->guest) {
    $groupIds  = $user->getAuthorisedGroups();
    $allGroups = UserGroupsHelper::getInstance()->getAll();

    if (!empty($groupIds)) {
        $highestGroupId = max($groupIds);

        if (isset($allGroups[$highestGroupId])) {
            $groupTitle = $allGroups[$highestGroupId]->title;
            $sanitized  = sanitizeForDataAttrComp($groupTitle);
            $dataUser   = 'user-' . $sanitized;
        }
    }
}

//	Get the alias of the current menu item
$active = $app->getMenu()->getActive();

//	Get Joomla template variables
$option    = $input->getCmd('option', '');
$view      = $input->getCmd('view', '');
$itemid    = $input->getCmd('Itemid', '');
$menu      = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$wrapper   = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';
$root      = Uri::root(true);

//	Assign template params
$bodyfont         = $this->params->get('bodyfont');
$bodyfontname     = $this->params->get('bodyfontname');
$bodymenu         = $this->params->get('bodymenu');
$bodygooglefont   = $this->params->get('bodygooglefont');
$bootstrapcdn     = $this->params->get('bootstrapcdn');
$bootstrapsource  = $this->params->get('bootstrapsource');
$bsfixjoomla      = $this->params->get('bsfixjoomla');
$bsicons          = $this->params->get('bsicons');
$bsthemes         = $this->params->get('bsthemes');
$codeafterbody    = $this->params->get('codeafterbody');
$codeafterhead    = $this->params->get('codeafterhead');
$codebeforebody   = $this->params->get('codebeforebody');
$codebeforehead   = $this->params->get('codebeforehead');
$atomicjs         = $this->params->get('atomicjs', 0);
$atomicstyles     = $this->params->get('atomicstyles', 0);
$customcssfile    = $this->params->get('customcssfile');
$customjs         = $this->params->get('customjs');
$fluidcontainer   = $this->params->get('fluidcontainer');
$fontawesome      = $this->params->get('fontawesome');
$fontawesomecdn   = $this->params->get('fontawesomecdn');
$gacode           = $this->params->get('gacode');
$headerfont       = $this->params->get('headerfont');
$headergooglefont = $this->params->get('headergooglefont');
$headerfontname   = $this->params->get('headerfontname');
$headerbackground = $this->params->get('headerbackground', '#ffffff');
$jqlibrary        = $this->params->get('jqlibrary');
$jquerycdn        = $this->params->get('jquerycdn');
$killgenerator    = $this->params->get('killgenerator');
$loadfavicons     = $this->params->get('loadfavicons');
$loadbsicons      = $this->params->get('loadbsicons');
$scrollreveal     = $this->params->get('scrollreveal');
$casspositions    = $this->params->get('casspositions');

$feediting = (int) $this->params->get('feediting', 0);
	$dataEditingAttr = $feediting === 1
    ? ' data-editing="no"'
    : '';

$theme = trim((string) $this->params->get('theme', ''));
	$dataThemeAttr = $theme !== ''
    ? ' data-theme="' . htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') . '"'
    : '';

$usergroupdata = (int) $this->params->get('usergroupdata', 0);
	if ($usergroupdata === 1 && !empty($dataUser)) {
	  $dataUserEscaped = htmlspecialchars($dataUser, ENT_QUOTES, 'UTF-8');
	  $dataUserAttr    = ' data-user="' . $dataUserEscaped . '"';
	} else {
	  $dataUserAttr = '';
	}

$bstheme					= $this->params->get('bstheme', '');
$bsthemecustom				= trim((string) $this->params->get('bsthemecustom', ''));
	if ($bstheme === 'custom') {
    $bstheme = $bsthemecustom !== '' ? strtolower($bsthemecustom) : '';
} elseif (!in_array($bstheme, ['light', 'dark', 'auto'])) {
    $bstheme = ''; // "None (Default)": omit data-bs-theme attribute
} else {
    $bstheme = strtolower($bstheme);
}

// Resolve the initial data-bs-theme attribute for the <html> tag.
// Bootstrap only recognizes 'light' and 'dark'. 'auto' is not valid.
// For 'auto', default to 'light' as a no-JS fallback — the inline
// script at the top of <head> immediately corrects it based on
// localStorage + system preference, preventing any flash.
$bsthemeInitial = $bstheme;
if ($bsthemeInitial === 'auto') {
    $bsthemeInitial = 'light';
}

$headerfontfamily   = getGoogleFontFamily($headerfont, 'header', $headerfontname);
$bodyfontfamily     = getGoogleFontFamily($bodyfont, 'body', $bodyfontname);
$isheadergooglefont = isGoogleFont($headerfont);
$isbodygooglefont   = isGoogleFont($bodyfont);

$containerClass = $fluidcontainer ? 'container-fluid' : 'container';

?>
<!DOCTYPE html>

<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $bsthemeInitial !== '' ? ' data-bs-theme="' . htmlspecialchars($bsthemeInitial, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>
  <?php echo $dataThemeAttr . $dataEditingAttr; ?>>
	<head>
		<?php // Inline theme resolution — runs before any CSS to prevent flash of wrong theme.
		if ($bstheme !== '') : ?>
		<script>(function(){var d=document.documentElement,s=localStorage.getItem('theme'),t=s||'<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}d.setAttribute('data-bs-theme',t);})()</script>
		<?php endif; ?>
		<?php
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

		// Preconnect hints for CDN resources
		$cdnHints = [];
		if ($bootstrapsource == 2 || $loadbsicons == 1 || ($bootstrapsource >= 6 && $bootstrapsource <= 14)) {
			$cdnHints[] = 'https://cdn.jsdelivr.net';
		}
		if ($fontawesome == 2 || $fontawesome == 3) {
			$cdnHints[] = 'https://cdnjs.cloudflare.com';
		}
		if ($scrollreveal == 1) {
			$cdnHints[] = 'https://unpkg.com';
		}
		foreach (array_unique($cdnHints) as $cdn) {
			echo '<link rel="preconnect" href="' . $cdn . '">';
			echo '<link rel="dns-prefetch" href="' . $cdn . '">';
		}
		?>
		<?php	//	Add custom code after opening head tag
			if($codeafterhead != null) : ?>
			<?php echo $codeafterhead;
			?>
		<?php endif; ?>

		<jdoc:include type="metas" />

		<?php	//	Remove Joomla generator tag
			if($killgenerator == 1) : ?>
			<?php $this->setMetaData('generator',''); ?>
		<?php endif; ?>

   	 	<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php
		// Get Joomla's dynamic page title and meta description
		$doc             = $app->getDocument();
		$pageTitle       = $doc->getTitle();
		$metaDescription = $doc->getMetaData('description');

		// Get the full current page URL
		$currentPageURL = Uri::getInstance()->toString();
		?>

    	<jdoc:include type="styles" />

		<?php	//	Load Bootstrap or Bootswatch theme.
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

		<?php	//	Load Google Fonts
			if(($headerfont == 1) && ($headerfontname != null)) : ?>
			<?php echo $headergooglefont; ?>
		<?php endif; ?>
		<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<?php echo $bodygooglefont; ?>
		<?php endif; ?>

		<?php
			$rootParts = [];
			$extraCSS  = [];

			if ($headerbackground !== "rgba(0, 0, 0, 0)") {
				$rootParts[] = '--atomic-header-background-color: ' . $headerbackground;
			}

			if ($headerfont != 2) {
				$rootParts[] = '--atomic-header-font: ' . $headerfontfamily;
			}

			if ($bodyfont != 2) {
				$rootParts[] = '--atomic-body-font: ' . ($bodyfont != 0 ? $bodyfontfamily : 'var(--bs-body-font-family)');
			}

			if ($feediting === 1) {
				$extraCSS[] = 'html[data-editing="no"] .jmodedit, html[data-editing="no"] .jmenuedit, html[data-editing="no"] div[role="tooltip"] { display: none !important; }';
			}

			if (!empty($rootParts) || !empty($extraCSS)) {
				$style = '<style>';
				if (!empty($rootParts)) {
					$style .= ':root {' . implode(';', $rootParts) . ';}';
				}
				$style .= implode('', $extraCSS);
				$style .= '</style>';
				echo $style;
			}
			?>

		<?php	//	Load FontAwesome
		if($fontawesome == 1 || $fontawesome == 6) : ?>
			<link rel="stylesheet" href="<?php echo $root ?>/media/system/css/joomla-fontawesome.min.css">
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php elseif($fontawesome == 3) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<?php elseif($fontawesome == 4 || $fontawesome == 5) : ?>
			<?php echo $fontawesomecdn; ?>
		<?php endif; ?>

		<?php	//	Load BS Icons
			if($loadbsicons == 1) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<?php endif; ?>

		<?php	//	Load Atomic CSS.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/atomic.min.css" type="text/css">
		<?php endif; ?>

		<?php	//	Load Atomic styles.
			if($atomicstyles == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/atomicstyles.min.css" type="text/css">
		<?php endif; ?>

		<?php	//	Load the template CSS file.
			if($customcssfile == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>

		<jdoc:include type="scripts" />

		<?php // Load jQuery ?>
		<?php if ($jqlibrary == 0) : ?>
			<script src="<?php echo $root ?>/media/vendor/jquery/js/jquery.min.js"></script>
		<?php elseif ($jqlibrary == 1) : ?>
			<script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha384-fgGyf7Mo7DURSOMnOy7ed+dkq5Job205Gnzu6QIg0BOHKaqt4D76Dt8VlDCzcMHV" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 2) : ?>
			<script src="https://code.jquery.com/jquery-4.0.0.slim.min.js" integrity="sha384-tcspKDb5tWvyRCOWzevlAeQgHeEzYdUHJpcgnIhcP9w4CnfD7DLAcS+k9QzLbRJO" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 3) : ?>
			<?php echo $jquerycdn ?>
		<?php endif; ?>

		<?php	//	Load BS Themeswitcher
			if($bsthemes == 1) : ?>
		<script>var defaultTheme = '<?php echo htmlspecialchars($bstheme ?: 'light', ENT_QUOTES, 'UTF-8'); ?>';</script>
				<script src="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/js/themeswitcher.min.js"></script>
		<?php endif; ?>

		<?php	//	Load Atomic JS
			if($atomicjs == 1) : ?>
				<script src="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/js/atomic.js"></script>
		<?php endif; ?>

		<?php	//	Load the template JavaScript file
			if($customjs == 1) : ?>
				<script src="<?php echo $root ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<?php endif; ?>

		<?php	//	Use Scroll Reveal
			if($scrollreveal == 1) : ?>
				<script src="https://unpkg.com/scrollreveal"></script>
		<?php endif; ?>

		<?php	//	Add custom code before closing head tag
			if($codebeforehead != null) : ?>
				<?php echo $codebeforehead;
		?>
		<?php endif; ?>

		<?php	//	Add Google Analytics tag if configured.
		if($gacode != null) : ?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gacode; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<?php echo $gacode; ?>');
		</script>
		<?php endif; ?>

	</head>

<?php
$activeAlias     = ($active !== null) ? htmlspecialchars($active->alias, ENT_QUOTES, 'UTF-8') : '';
$defaultBodyClass = 'contentpane component ' . $option . ' ' . $wrapper . ' view-' . $view
    . ($itemid    ? ' itemid-' . $itemid   : '')
    . ($pageclass ? ' ' . $pageclass       : '')
    ;
?>
<?php if ($bodymenu == 1) : // Append Class ?>
	<body class="<?php echo trim($defaultBodyClass . ' ' . $activeAlias); ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($bodymenu == 2) : // Append ID ?>
	<body id="<?php echo $activeAlias; ?>" class="<?php echo $defaultBodyClass; ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($bodymenu == 3) : // Append Class & ID ?>
	<body id="<?php echo $activeAlias; ?>" class="<?php echo trim($defaultBodyClass . ' ' . $activeAlias); ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($bodymenu == 4) : // Replace — Class only ?>
	<body class="<?php echo $activeAlias; ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($bodymenu == 5) : // Replace — ID only ?>
	<body id="<?php echo $activeAlias; ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($bodymenu == 6) : // Replace — Class & ID ?>
	<body id="<?php echo $activeAlias; ?>" class="<?php echo $activeAlias; ?>"<?php echo $dataUserAttr; ?>>
<?php elseif ($casspositions == 1) : ?>
	<body class="cassiopeia"<?php echo $dataUserAttr; ?>>
<?php else : ?>
	<body class="<?php echo $defaultBodyClass; ?>"<?php echo $dataUserAttr; ?>>
<?php endif; ?>

	<?php	//	Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody; ?>
	<?php endif; ?>

	<?php if ($this->countModules('alert', true)) : ?>
		<div class="alertbar">
			<div class="<?php echo $containerClass; ?>">
				<div class="row">
					<jdoc:include type="modules" name="alert" title="Alerts" style="none" />
				</div>
			</div>
		</div>
	<?php endif; ?>

    <main>
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</main>

		<?php	//	Load Bootstrap JS
			if($bootstrapsource == 1) : ?>
				<script src="<?php echo $root ?>/media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 2) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource >= 6 && $bootstrapsource <= 14) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource == 3 || $bootstrapsource == 4) : ?>
				<?php HTMLHelper::_('bootstrap.framework'); ?>
		<?php endif; ?>

		<?php	//	Add custom code before closing body tag
			if($codebeforebody != null) : ?>
			<?php echo $codebeforebody; ?>
		<?php endif; ?>

	<?php if ($this->countModules('debug')) : ?>
		<jdoc:include type="modules" name="debug" title="Debug" style="none" />
	<?php endif; ?>
</body>
</html>
