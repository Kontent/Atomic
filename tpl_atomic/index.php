<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Helper\UserGroupsHelper;

$app   = Factory::getApplication();
$input = $app->getInput();
$wa    = $this->getWebAssetManager();

//	Get user group for attribute

function sanitizeForDataAttr($string) {
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
            $sanitized  = sanitizeForDataAttr($groupTitle);
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
$bodyfont					= $this->params->get('bodyfont');
$bodyfontname				= $this->params->get('bodyfontname');
$bodymenu					= $this->params->get('bodymenu');
$bodygooglefont				= $this->params->get('bodygooglefont');
$bootstrapcdn				= $this->params->get('bootstrapcdn');
$bootstrapsource			= $this->params->get('bootstrapsource', 2);
$bsfixjoomla				= $this->params->get('bsfixjoomla', 1);
$bsthemes					= $this->params->get('bsthemes');
$codeafterbody				= $this->params->get('codeafterbody');
$codeafterhead				= $this->params->get('codeafterhead');
$codebeforebody				= $this->params->get('codebeforebody');
$codebeforehead				= $this->params->get('codebeforehead');
$copyright					= $this->params->get('copyright', '');
$copyrighttxt				= $this->params->get('copyrighttxt');
$atomicjs					= $this->params->get('atomicjs', 0);
$atomicstyles				= $this->params->get('atomicstyles', 0);
$customcssfile				= $this->params->get('customcssfile');
$customjs					= $this->params->get('customjs');
$fluidcontainer				= $this->params->get('fluidcontainer');
$fontawesome				= $this->params->get('fontawesome');
$fontawesomecdn				= $this->params->get('fontawesomecdn');
$gacode						= $this->params->get('gacode');
$headerfont					= $this->params->get('headerfont');
$headergooglefont			= $this->params->get('headergooglefont');
$headerfontname				= $this->params->get('headerfontname');
$jqlibrary					= $this->params->get('jqlibrary');
$jquerycdn					= $this->params->get('jquerycdn');
$killgenerator				= $this->params->get('killgenerator');
$loadfavicons				= $this->params->get('loadfavicons');
$loadbsicons				= $this->params->get('loadbsicons');
$logo						= $this->params->get('logo');
$scrollreveal				= $this->params->get('scrollreveal');
$sitedescription			= $this->params->get('sitedescription');
$sitetitle					= $this->params->get('sitetitle');
$systemFontHeader			= $this->params->get('systemFontHeader', '');
$systemFontBody				= $this->params->get('systemFontBody', '');
$casspositions				= $this->params->get('casspositions');
$stickyhead					= (int) $this->params->get('stickyheader', $this->params->get('stickyhead', 0));
$headerbackground			= $this->params->get('headerbackground', 'rgba(0, 0, 0, 0)');
$bootscolumns				= $this->params->get('bootscolumns', '2-8-2');
$headercolumns				= $this->params->get('headercolumns', '12');
$footercolumns				= $this->params->get('footercolumns', '12');

$feediting       			= (int) $this->params->get('feediting', 0);
  	$dataEditingAttr  = $feediting === 1
    ? ' data-editing="no"'
    : '';

$theme 						= trim((string) $this->params->get('theme', ''));
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

$headerfontfamily			= getGoogleFontFamily($headerfont, 'header', $headerfont == 13 ? $systemFontHeader : $headerfontname);
$bodyfontfamily				= getGoogleFontFamily($bodyfont, 'body', $bodyfont == 13 ? $systemFontBody : $bodyfontname);
$isheadergooglefont			= isGoogleFont($headerfont);
$isbodygooglefont			= isGoogleFont($bodyfont);

$containerClass				= $fluidcontainer ? 'container-fluid' : 'container';

$typescale = $this->params->get('typescale', '0');
$typescaleMap = ['1' => 'major-third', '2' => 'minor-third', '3' => 'major-second', '4' => 'minor-second'];
$dataTypescaleAttr = isset($typescaleMap[(string) $typescale]) ? ' data-typescale="' . $typescaleMap[(string) $typescale] . '"' : '';

// Legacy integer mapping for bootscolumns (backward compatibility with pre-5.1 stored values)
$legacyBodyMap = ['0' => '2-8-2', '1' => '2-7-3', '2' => '2-6-4', '3' => '3-6-3', '4' => '4-4-4'];
if (array_key_exists((string) $bootscolumns, $legacyBodyMap)) {
	$bootscolumns = $legacyBodyMap[(string) $bootscolumns];
}

// Parse header column spec
$headerParts    = array_map('intval', explode('-', (string) $headercolumns));
$headerColCount = count($headerParts);

// Parse footer column spec
$footerParts    = array_map('intval', explode('-', (string) $footercolumns));
$footerColCount = count($footerParts);

// ── PreloadManager preconnect hints ─────────────────────────
$preloadManager = $this->getPreloadManager();

if ($isheadergooglefont || $isbodygooglefont) {
	$preloadManager->preconnect('https://fonts.googleapis.com');
	$preloadManager->preconnect('https://fonts.gstatic.com', ['crossorigin' => '']);
}

$cdnHints = [];
if ($bootstrapsource == 2 || $loadbsicons == 1) {
	$cdnHints[] = 'https://cdn.jsdelivr.net';
}
if ($fontawesome == 2 || $fontawesome == 3) {
	$cdnHints[] = 'https://cdnjs.cloudflare.com';
}
if ($bootstrapsource >= 6 && $bootstrapsource <= 14) {
	$cdnHints[] = 'https://cdn.jsdelivr.net';
}
if ($scrollreveal == 1) {
	$cdnHints[] = 'https://unpkg.com';
}
foreach (array_unique($cdnHints) as $cdn) {
	$preloadManager->preconnect($cdn);
}

// ── Lazy-loaded third-party stylesheets ─────────────────────
// Google Fonts: register built-in fonts (cases 3–12) with media="print" trick
if ($isheadergooglefont || $isbodygooglefont) {
	$fontsToLoad = array_unique([$headerfont, $bodyfont]);
	foreach ($fontsToLoad as $font) {
		if ($font && isGoogleFont($font)) {
			$fontHtml = getGoogleFontLink($font);
			if (preg_match('/href="([^"]+)"/', $fontHtml, $m)) {
				$wa->registerAndUseStyle('atomic.font.' . $font, $m[1], [], [
					'media' => 'print', 'onload' => "this.media='all'"
				]);
			}
		}
	}
}

// FontAwesome CSS (cases 1,2,6 — JS and custom snippet cases stay manual)
if ($fontawesome == 1 || $fontawesome == 6) {
	$wa->registerAndUseStyle('atomic.fontawesome', 'media/system/css/joomla-fontawesome.min.css', [], [
		'media' => 'print', 'onload' => "this.media='all'"
	]);
} elseif ($fontawesome == 2) {
	$wa->registerAndUseStyle('atomic.fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css', [], [
		'integrity' => 'sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==',
		'crossorigin' => 'anonymous', 'referrerpolicy' => 'no-referrer',
		'media' => 'print', 'onload' => "this.media='all'"
	]);
}

// Bootstrap Icons
if ($loadbsicons == 1) {
	$wa->registerAndUseStyle('atomic.bsicons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css', [], [
		'media' => 'print', 'onload' => "this.media='all'"
	]);
}

// ── Web Asset Manager: Atomic CSS  ───────────────────────────
if ($bsfixjoomla == 1) {
	$wa->useStyle('template.atomic.bs5css');
}
if ($atomicstyles == 1) {
	$wa->useStyle('template.atomic.atomicstyles');
}
if ($customcssfile == 1) {
	$wa->useStyle('template.atomic.css');
}

// ── Web Asset Manager: Atomic JS ────────────────────────────
if ($bsthemes == 1) {
	$wa->useScript('template.atomic.themeswitcher');
}
if ($atomicjs == 1) {
	$wa->useScript('template.atomic.atomicjs');
}
if ($customjs == 1) {
	$wa->useScript('template.atomic.js');
}

?>
<!DOCTYPE html>

<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $bsthemeInitial !== '' ? ' data-bs-theme="' . htmlspecialchars($bsthemeInitial, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>
  <?php echo $dataThemeAttr . $dataEditingAttr . $dataTypescaleAttr; ?>>
	<head>
		<?php // Inline theme resolution — runs before any CSS to prevent flash of wrong theme.
		if ($bstheme !== '') : ?>
		<script>(function(){var d=document.documentElement,s=localStorage.getItem('theme'),t=s||'<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}d.setAttribute('data-bs-theme',t);})()</script>
		<?php endif; ?>
		<?php // Preconnect hints and Google Font links are now handled by
			  // PreloadManager and Web Asset Manager
			  // in the PHP section above. ?>

		<?php	//	Add custom code after opening head tag
			if($codeafterhead != null) : ?>
			<?php echo $codeafterhead;
			?>	
		<?php endif; ?>
		<jdoc:include type="metas" />
		<?php	//	Remove Joomla generator tag
			if($killgenerator	 == 1) : ?>
			<?php $this->setMetaData('generator',''); ?>
		<?php endif; ?>		
		
		<?php
		//	Favicons
		//	0 = Default: load from media folder (default Atomic favicons)
		//	1 = Template: load from template favicons folder (custom favicons)
		//	2 = Site: load from web root (custom favicons)
		if ($loadfavicons == 2) :
			$faviconBase = $root;
		elseif ($loadfavicons == 1) :
			$faviconBase = $root . '/templates/' . $this->template . '/favicons';
		else :
			$faviconBase = $root . '/media/templates/site/' . $this->template . '/favicons';
		endif;
		?>
			<link rel="icon" type="image/svg+xml" href="<?php echo $faviconBase; ?>/favicon.svg" />
			<link rel="alternate icon" type="image/png" href="<?php echo $faviconBase; ?>/favicon-96x96.png" sizes="96x96" />
			<link rel="alternate icon" href="<?php echo $faviconBase; ?>/favicon.ico" />
			<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $faviconBase; ?>/apple-touch-icon.png" />
			<?php $maskColor = $this->params->get('maskiconcolor', '#000000'); ?>
			<link rel="mask-icon" href="<?php echo $faviconBase; ?>/favicon.svg" color="<?php echo htmlspecialchars($maskColor, ENT_QUOTES, 'UTF-8'); ?>" />
			<link rel="manifest" href="<?php echo $faviconBase; ?>/site.webmanifest" />
			
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="color-scheme" content="light dark">

		<?php
		// Retrieve Joomla template settings (1 = Show, 0 = Hide)
		$socialtitle = $this->params->get('socialtitle', 0);
		$socialurl = $this->params->get('socialurl', 0);
		$socialdescription = $this->params->get('socialdescription', 0);
		
		// Retrieve image selections
		$socialthumbgoogle = $this->params->get('socialthumbgoogle', '');
		$socialthumbfacebook = $this->params->get('socialthumbfacebook', '');
		$socialthumbtwitter = $this->params->get('socialthumbtwitter', '');
		
		// Get Joomla’s dynamic page title and meta description
		$pageTitle       = $this->title;
		$metaDescription = $this->description;

		// Get the full current page URL
		$currentPageURL = Uri::getInstance()->toString();
		
		// Base URL from template (for cleaning relative image URLs)
		$baseurl = $this->baseurl;
		
		// Function to clean image URLs by removing query parameters and ensuring a full URL
		function cleanImageURL($image, $baseurl) {
			if (!empty($image)) {
				$parsed = parse_url($image);
				$cleanedPath = isset($parsed['scheme'])
					? $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path']
					: ltrim($parsed['path'], '/'); // Ensure no leading slash
		
				// Prepend base URL only if the image doesn't already have a scheme
				return isset($parsed['scheme'])
					? $cleanedPath
					: rtrim($baseurl, '/') . '/' . $cleanedPath; // Remove extra slash
			}
			return '';
		}
		
		// Clean image URLs
		$socialthumbgoogle = cleanImageURL($socialthumbgoogle, $baseurl);
		$socialthumbfacebook = cleanImageURL($socialthumbfacebook, $baseurl);
		$socialthumbtwitter = cleanImageURL($socialthumbtwitter, $baseurl);
		?>
		
		<?php if ($socialtitle) : ?>
			<meta itemprop="name" content="<?php echo $pageTitle; ?>">
		<?php endif; ?>
		<?php if (!empty($socialthumbgoogle)) : ?>
			<meta itemprop="image" content="<?php echo $socialthumbgoogle; ?>">
		<?php endif; ?>
		
		<?php if ($socialurl) : ?>
			<meta property="og:url" content="<?php echo $currentPageURL; ?>">
		<?php endif; ?>
		<?php if ($socialtitle || $socialdescription || $socialurl) : ?>
			<meta property="og:type" content="website">
		<?php endif; ?>
		<?php if ($socialtitle) : ?>
			<meta property="og:title" content="<?php echo $pageTitle; ?>">
		<?php endif; ?>
		<?php if ($socialdescription && !empty($metaDescription)) : ?>
			<meta property="og:description" content="<?php echo $metaDescription; ?>">
		<?php endif; ?>
		<?php if (!empty($socialthumbfacebook)) : ?>
			<meta property="og:image" content="<?php echo $socialthumbfacebook; ?>">
		<?php endif; ?>
		
		<?php if ($socialurl) : ?>
			<meta property="twitter:url" content="<?php echo $currentPageURL; ?>">
		<?php endif; ?>
		<?php if ($socialtitle) : ?>
			<meta name="twitter:title" content="<?php echo $pageTitle; ?>">
		<?php endif; ?>
		<?php if ($socialdescription && !empty($metaDescription)) : ?>
			<meta name="twitter:description" content="<?php echo $metaDescription; ?>">
		<?php endif; ?>
		<?php if (!empty($socialthumbtwitter)) : ?>
			<meta name="twitter:image" content="<?php echo $socialthumbtwitter; ?>">
			<meta name="twitter:card" content="summary_large_image">
		<?php endif; ?>

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
		<?php	//	Load FontAwesome JS or custom snippet (CSS cases handled by Web Asset Manager)
		if($fontawesome == 3) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<?php elseif($fontawesome == 4 || $fontawesome == 5) : ?>
			<?php echo $fontawesomecdn; ?>
		<?php endif; ?>

		<?php //	Web Asset Manager outputs: lazy Google Fonts, lazy FontAwesome CSS,
			  //	lazy BS Icons, atomic.min.css, atomicstyles.min.css, template.css,
			  //	plus Joomla extension styles. ?>
		<jdoc:include type="styles" />

		<?php // Fallback for lazy-loaded stylesheets when JavaScript is disabled ?>
		<noscript>
		<?php if ($fontawesome == 1 || $fontawesome == 6) : ?>
			<link rel="stylesheet" href="<?php echo $root ?>/media/system/css/joomla-fontawesome.min.css">
		<?php elseif ($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
		<?php endif; ?>
		<?php if ($loadbsicons == 1) : ?>
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<?php endif; ?>
		</noscript>
			
		<?php // Load jQuery ?>
		<?php if ($jqlibrary == 0) : ?>
			<script src="/media/vendor/jquery/js/jquery.min.js"></script>
		<?php elseif ($jqlibrary == 1) : ?>
			<script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha384-fgGyf7Mo7DURSOMnOy7ed+dkq5Job205Gnzu6QIg0BOHKaqt4D76Dt8VlDCzcMHV" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 2) : ?>
			<script src="https://code.jquery.com/jquery-4.0.0.slim.min.js" integrity="sha384-tcspKDb5tWvyRCOWzevlAeQgHeEzYdUHJpcgnIhcP9w4CnfD7DLAcS+k9QzLbRJO" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 3) : ?>
			<?php echo $jquerycdn ?>
		<?php endif; ?>

		<?php	//	Theme switcher default theme (must load before themeswitcher.min.js)
			if($bsthemes == 1) : ?>
		<script>var defaultTheme = '<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>';</script>
		<?php endif; ?>

		<?php //	Web Asset Manager outputs: themeswitcher.min.js, atomic.js, template.js,
			  //	plus Joomla core scripts. ?>
		<jdoc:include type="scripts" />

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
$defaultBodyClass = 'site ' . $option . ' ' . $wrapper . ' view-' . $view
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

	<?php
	// Mobile menu (defined early so the offcanvas panel can render outside the header)
	$hasMobile = $this->countModules('mobilemenu', true);

	// Do we have any header at all?
	$hasHeaderContent = $hasMobile
		|| $this->countModules('header', true)
		|| $this->countModules('header-center', true)
		|| $this->countModules('header-right', true)
		|| $this->countModules('topmenu', true)
		|| $logo
		|| $sitetitle
		|| $sitedescription
		|| $bsthemes;

	if ($hasHeaderContent):
		// Sticky class?
		$headerClass = ($stickyhead === 1) ? 'sticky' : '';

		// Brand = logo OR title OR description
		$hasBrand = $logo || $sitetitle || $sitedescription;

		// Header module positions
		$hasHeaderMods   = $this->countModules('header', true);
		$hasHeaderCenter = $this->countModules('header-center', true);
		$hasHeaderRight  = $this->countModules('header-right', true);
		$hasThemeSwitch  = !empty($bsthemes);
	?>
	<header<?= $headerClass ? ' class="' . $headerClass . '"' : '' ?>>
	  <div class="<?= $containerClass ?>">

		<?php if ($casspositions == 1): ?>
		  <jdoc:include type="modules" name="topbar" style="none" />
		  <jdoc:include type="modules" name="below-top" style="none" />
		<?php endif; ?>

		<?php if ($hasMobile || $hasBrand || $hasHeaderMods || $hasHeaderCenter || $hasHeaderRight || $hasThemeSwitch): ?>
		<div class="header-row row align-items-center">

		  <?php if ($headerColCount >= 3): ?>
		  <!-- 3-column header -->
		  <div class="col-12 col-md-<?php echo $headerParts[0]; ?>">
			<div class="d-flex flex-wrap align-items-center gap-3">
			  <?php if ($hasMobile): ?>
				<div class="d-sm-none">
				  <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilemenuOffcanvas" aria-controls="mobilemenuOffcanvas" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
				</div>
			  <?php endif; ?>
			  <div class="d-flex align-items-center gap-2 flex-grow-1">
				<?php if ($logo): ?>
				  <div id="logo" class="flex-shrink-0">
					<a href="<?= $this->baseurl ?>">
					  <img src="<?= $this->baseurl . '/' . htmlspecialchars($logo) ?>"
						   alt="<?= htmlspecialchars($sitetitle ?: '') ?>"
						   loading="eager" decoding="async" fetchpriority="high" />
					</a>
				  </div>
				<?php endif; ?>
				<?php if ($sitetitle || $sitedescription): ?>
				  <div class="site-info d-flex flex-column">
					<?php if ($sitetitle): ?>
					  <div class="site-title">
						<a href="<?= $this->baseurl ?>"><?= $sitetitle ?></a>
					  </div>
					<?php endif; ?>
					<?php if ($sitedescription): ?>
					  <div class="site-description">
						<?= $sitedescription ?>
					  </div>
					<?php endif; ?>
				  </div>
				<?php endif; ?>
			  </div>
			  <?php if ($hasHeaderMods): ?>
				<jdoc:include type="modules" name="header" style="none" />
			  <?php endif; ?>
			</div>
		  </div>
		  <div class="col-12 col-md-<?php echo $headerParts[1]; ?>">
			<?php if ($hasHeaderCenter): ?>
			  <jdoc:include type="modules" name="header-center" style="none" />
			<?php endif; ?>
		  </div>
		  <div class="col-12 col-md-<?php echo $headerParts[2]; ?>">
			<div class="d-flex align-items-center gap-3 ms-auto flex-wrap justify-content-end">
			  <?php if ($hasHeaderRight): ?>
				<jdoc:include type="modules" name="header-right" style="none" />
			  <?php endif; ?>
			  <?php if ($hasThemeSwitch): ?>
				<?= LayoutHelper::render('header.styleswitcher', ['bsthemes' => $bsthemes]); ?>
			  <?php endif; ?>
			</div>
		  </div>

		  <?php elseif ($headerColCount >= 2): ?>
		  <!-- 2-column header -->
		  <div class="col-12 col-md-<?php echo $headerParts[0]; ?>">
			<div class="d-flex flex-wrap align-items-center gap-3">
			  <?php if ($hasMobile): ?>
				<div class="d-sm-none">
				  <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilemenuOffcanvas" aria-controls="mobilemenuOffcanvas" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
				</div>
			  <?php endif; ?>
			  <div class="d-flex align-items-center gap-2 flex-grow-1">
				<?php if ($logo): ?>
				  <div id="logo" class="flex-shrink-0">
					<a href="<?= $this->baseurl ?>">
					  <img src="<?= $this->baseurl . '/' . htmlspecialchars($logo) ?>"
						   alt="<?= htmlspecialchars($sitetitle ?: '') ?>"
						   loading="eager" decoding="async" fetchpriority="high" />
					</a>
				  </div>
				<?php endif; ?>
				<?php if ($sitetitle || $sitedescription): ?>
				  <div class="site-info d-flex flex-column">
					<?php if ($sitetitle): ?>
					  <div class="site-title">
						<a href="<?= $this->baseurl ?>"><?= $sitetitle ?></a>
					  </div>
					<?php endif; ?>
					<?php if ($sitedescription): ?>
					  <div class="site-description">
						<?= $sitedescription ?>
					  </div>
					<?php endif; ?>
				  </div>
				<?php endif; ?>
			  </div>
			  <?php if ($hasHeaderMods): ?>
				<jdoc:include type="modules" name="header" style="none" />
			  <?php endif; ?>
			</div>
		  </div>
		  <div class="col-12 col-md-<?php echo $headerParts[1]; ?>">
			<div class="d-flex align-items-center gap-3 ms-auto flex-wrap justify-content-end">
			  <?php if ($hasHeaderRight): ?>
				<jdoc:include type="modules" name="header-right" style="none" />
			  <?php endif; ?>
			  <?php if ($hasThemeSwitch): ?>
				<?= LayoutHelper::render('header.styleswitcher', ['bsthemes' => $bsthemes]); ?>
			  <?php endif; ?>
			</div>
		  </div>

		  <?php else: ?>
		  <!-- 1-column header -->
		  <div class="col-12">
			<div class="d-flex flex-wrap align-items-center gap-3">
			  <?php if ($hasMobile): ?>
				<div class="d-sm-none">
				  <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilemenuOffcanvas" aria-controls="mobilemenuOffcanvas" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
				</div>
			  <?php endif; ?>
			  <jdoc:include type="modules" name="header" style="none" />
			  <?php if ($hasThemeSwitch): ?>
				<?= LayoutHelper::render('header.styleswitcher', ['bsthemes' => $bsthemes]); ?>
			  <?php endif; ?>
			</div>
		  </div>
		  <?php endif; ?>

		</div>
		<?php endif; ?>

		<?php if ($this->countModules('topmenu', true)): ?>
		<div class="header-bottom row align-items-center">
		  <div class="col-12">
			<nav class="navigation">
			  <div class="nav-collapse">
				<jdoc:include type="modules" name="topmenu" style="none" />
				<?php if ($casspositions == 1): ?>
				  <jdoc:include type="modules" name="menu" style="none" />
				<?php endif; ?>
			  </div>
			</nav>
		  </div>
		</div>
		<?php endif; ?>

	  </div>
	</header>
	<?php endif; ?>

	<?php // Mobile menu offcanvas panel — rendered outside header to avoid stacking context issues ?>
	<?php if ($hasMobile): ?>
	<div class="offcanvas mobilemenu-offcanvas offcanvas-start" data-bs-backdrop="true" data-bs-scroll="false" tabindex="-1" id="mobilemenuOffcanvas" aria-labelledby="mobilemenuLabel">
		<div class="offcanvas-header">
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<jdoc:include type="modules" name="mobilemenu" style="none" />
		</div>
	</div>
	<?php endif; ?>

	<main id="main-content">
	  <div class="<?php echo $containerClass; ?>">
		
		<?php if ($this->countModules('hero', true)) : ?>
			<div class="hero">
				<jdoc:include type="modules" name="hero" style="none" />
			</div>
		<?php endif; ?>
		
		<?php if ($casspositions == 1
		   && ($this->countModules('banner', true)
			 || $this->countModules('top-a', true)
			 || $this->countModules('top-b', true))
		) : ?>
		  <div class="row">
			<div class="col">
			  <jdoc:include type="modules" name="banner"  style="none" />
			  <jdoc:include type="modules" name="top-a"   style="none" />
			  <jdoc:include type="modules" name="top-b"   style="none" />
			</div>
		  </div>
		<?php endif; ?>
	
		<?php
		  $showLeft  = ($casspositions == 1 && $this->countModules('sidebar-left', true))
					 || $this->countModules('leftbody', true);
		  $showRight = ($casspositions == 1 && $this->countModules('sidebar-right', true))
					 || $this->countModules('rightbody', true);

		  // Parse body column spec (string values e.g. "2-8-2", "4-8", "12")
		  $bodyParts     = array_map('intval', explode('-', (string) $bootscolumns));
		  $bodyPartCount = count($bodyParts);

		  if ($bodyPartCount === 1) {
			// Single column — hide both sidebars
			$showLeft  = false;
			$showRight = false;
			$mainClass = "col-12";
		  } elseif ($bodyPartCount === 2) {
			// Two-column layout
			if ($bodyParts[0] < $bodyParts[1]) {
			  // Left sidebar + content (e.g. 2-10, 4-8)
			  $showRight = false;
			  if ($showLeft) {
				$leftClass = "d-none d-lg-block col-lg-{$bodyParts[0]}";
				$mainClass = "col-12 col-lg-{$bodyParts[1]}";
			  } else {
				$mainClass = "col-12";
			  }
			} else {
			  // Content + right sidebar (e.g. 8-4, 10-2)
			  $showLeft = false;
			  if ($showRight) {
				$mainClass  = "col-12 col-lg-{$bodyParts[0]}";
				$rightClass = "col-12 col-lg-{$bodyParts[1]}";
			  } else {
				$mainClass = "col-12";
			  }
			}
		  } else {
			// Three-column layout
			if ($showLeft && $showRight) {
			  [$l, $m, $r] = $bodyParts;
			  $leftClass  = "d-none d-lg-block col-lg-{$l}";
			  $mainClass  = "col-12 col-lg-{$m}";
			  $rightClass = "col-12 col-lg-{$r}";
			} elseif ($showLeft) {
			  $sideWidth = $bodyParts[0];
			  $mainWidth = 12 - $sideWidth;
			  $leftClass = "d-none d-lg-block col-lg-{$sideWidth}";
			  $mainClass = "col-12 col-lg-{$mainWidth}";
			} elseif ($showRight) {
			  $sideWidth = $bodyParts[2];
			  $mainWidth = 12 - $sideWidth;
			  $mainClass  = "col-12 col-lg-{$mainWidth}";
			  $rightClass = "col-12 col-lg-{$sideWidth}";
			} else {
			  $mainClass = "col-12";
			}
		  }
		?>
	
		<div class="row">
		  <?php if ($showLeft) : ?>
			<div class="container-sidebar-left mt-4 <?= $leftClass; ?>">
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="sidebar-left" style="none" />
			  <?php endif; ?>
			  <jdoc:include type="modules" name="leftbody"     style="none" />
			</div>
		  <?php endif; ?>
	
		  <div class="container-component mt-4 <?= $mainClass; ?>">
			<jdoc:include type="modules" name="breadcrumbs" style="none" />
			<?php if ($this->countModules('abovebody', true)) : ?>
			<div class="module-position-abovebody mb-3">
			  <jdoc:include type="modules" name="abovebody"   style="none" />
			</div>
			<?php endif; ?>
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="main-top"  style="none" />
			<?php endif; ?>
			<jdoc:include type="message" />
			<jdoc:include type="component" />
			<?php if ($this->countModules('belowbody', true)) : ?>
			<div class="module-position-belowbody mt-3">
			  <jdoc:include type="modules" name="belowbody"   style="none" />
			</div>
			<?php endif; ?>
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="main-bottom" style="none" />
			<?php endif; ?>
		  </div>
	
		  <?php if ($showRight) : ?>
			<div class="container-sidebar-right mt-4 <?= $rightClass; ?>">
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="sidebar-right" style="none" />
			<?php endif; ?>
			  <jdoc:include type="modules" name="rightbody"      style="none" />
			</div>
		  <?php endif; ?>
		</div>
	
		<?php if ($casspositions == 1 && $this->countModules('bottom-a', true)) : ?>
		  <div class="row">
			<jdoc:include type="modules" name="bottom-a" style="none" />
		  </div>
		<?php endif; ?>
	
		<?php if ($casspositions == 1 && $this->countModules('bottom-b', true)) : ?>
		  <div class="row">
			<jdoc:include type="modules" name="bottom-b" style="none" />
		  </div>
		<?php endif; ?>
	
	  </div>
	</main>

	<?php
	$showCopyright = ($copyright !== '' && $copyright !== '0' && (int) $copyright !== 0);
	$hasFooterMods = $this->countModules('footer', true)
		|| $this->countModules('footer-center', true)
		|| $this->countModules('footer-right', true);
	?>
	<?php if ($hasFooterMods || $showCopyright) : ?>
		<footer>
			<div class="<?php echo $containerClass; ?>">
				<div class="row">
					<?php if ($footerColCount >= 3): ?>
						<div class="col-12 col-md-<?php echo $footerParts[0]; ?>">
							<jdoc:include type="modules" name="footer" title="Footer" style="none" />
						</div>
						<div class="col-12 col-md-<?php echo $footerParts[1]; ?>">
							<jdoc:include type="modules" name="footer-center" title="Footer Center" style="none" />
						</div>
						<div class="col-12 col-md-<?php echo $footerParts[2]; ?>">
							<jdoc:include type="modules" name="footer-right" title="Footer Right" style="none" />
						</div>
					<?php elseif ($footerColCount == 2): ?>
						<div class="col-12 col-md-<?php echo $footerParts[0]; ?>">
							<jdoc:include type="modules" name="footer" title="Footer" style="none" />
						</div>
						<div class="col-12 col-md-<?php echo $footerParts[1]; ?>">
							<jdoc:include type="modules" name="footer-right" title="Footer Right" style="none" />
						</div>
					<?php else: ?>
						<div class="col-12">
							<jdoc:include type="modules" name="footer" title="Footer" style="none" />
						</div>
					<?php endif; ?>
				</div>
				<?php if ($showCopyright) : ?>
				<div class="row">
					<div class="col-12 copyright">
						<?php
						$year     = date('Y');
						$sitename = htmlspecialchars($app->get('sitename'));
						switch ((string) $copyright) {
							case '2': // Year & custom notice
								echo '&copy;' . $year . ' ' . ($copyrighttxt ?: $sitename);
								break;
							case '3': // Custom notice only
								echo ($copyrighttxt ?: ('&copy;' . $year . ' ' . $sitename));
								break;
							default: // '1' or legacy → Year & site title
								echo '&copy;' . $year . ' ' . $sitename;
								break;
						}
						?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</footer>
	<?php endif; ?>
		
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
