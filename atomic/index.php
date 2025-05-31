<?php
/**
 * @copyright	Copyright (C) 2008-2023 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();
$user = Factory::getApplication()->getIdentity();

$input = $app->getInput();
$wa = $this->getWebAssetManager();

//	Get user group for attribute
use Joomla\CMS\Helper\UserGroupsHelper;

function sanitizeForDataAttr($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

$user = $app->getIdentity();
$dataUser = '';

if (!$user->guest) {
    $groupIds = $user->getAuthorisedGroups();
    $allGroups = UserGroupsHelper::getInstance()->getAll();

    if (!empty($groupIds)) {
        $highestGroupId = max($groupIds);

        if (isset($allGroups[$highestGroupId])) {
            $groupTitle = $allGroups[$highestGroupId]->title;
            $sanitized = sanitizeForDataAttr($groupTitle);
            $dataUser = 'user-' . $sanitized;
        }
    }
}

//	Get the application object for things like displaying the site name 

//	Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

//	Get Joomla template variables
$this->baseurl = JUri::base();
$option		= $input->getCmd('option', '');
$view		= $input->getCmd('view', '');
$itemid		= $input->getCmd('Itemid', '');
$menu		= $app->getMenu()->getActive();
$pageclass 	= $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$wrapper 	= $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';
$root 		= JUri::root(true);

//	Assign template params
$bodyfont					= $this->params->get('bodyfont');
$bodyfontname				= $this->params->get('bodyfontname');
$bodymenu					= $this->params->get('bodymenu');
$bodygooglefont				= $this->params->get('bodygooglefont');
$bootstrapcdn				= $this->params->get('bootstrapcdn');
$bootstrapsource			= $this->params->get('bootstrapsource');
$bsfixjoomla				= $this->params->get('bsfixjoomla');
$bsicons					= $this->params->get('bsicons');
$bsthemes					= $this->params->get('bsthemes');
$codeafterbody				= $this->params->get('codeafterbody');
$codeafterhead				= $this->params->get('codeafterhead');
$codebeforebody				= $this->params->get('codebeforebody');
$codebeforehead				= $this->params->get('codebeforehead');
$copyright					= $this->params->get('copyright');
$copyrighttxt				= $this->params->get('copyrighttxt');
$customcssfile				= $this->params->get('customcssfile');
$customjs					= $this->params->get('customjs');
$fluidcontainer				= $this->params->get('fluidcontainer');
$fontawesome				= $this->params->get('fontawesome');
$fontawesomecdn				= $this->params->get('fontawesomecdn');
$gacode						= $this->params->get('gacode');
$headerfont					= $this->params->get('headerfont');
$headergooglefont			= $this->params->get('headergooglefont');
$headerfontname				= $this->params->get('headerfontname');
$instant					= $this->params->get('instant');
$jqlibrary					= $this->params->get('jqlibrary');
$jquerycdn					= $this->params->get('jquerycdn');
$killgenerator				= $this->params->get('killgenerator');
$loadfavicons				= $this->params->get('loadfavicons');
$loadbsicons				= $this->params->get('loadbsicons');
$logo						= $this->params->get('logo');
$scrollreveal				= $this->params->get('scrollreveal');
$sitedescription			= $this->params->get('sitedescription');
$sitetitle					= $this->params->get('sitetitle');
$casspositions				= $this->params->get('casspositions');
$stickyhead					= $this->params->get('stickyhead');
$sidebarmenu				= $this->params->get('sidebarmenu');
$headerbackground			= $this->params->get('headerbackground', '#ffffff');
$socialtitle				= $this->params->get('socialtitle');
$socialdescription			= $this->params->get('socialdescription');
$socialthumbgoogle			= $this->params->get('socialthumbgoogle');
$socialthumbfacebook		= $this->params->get('socialthumbfacebook');
$socialthumbtwitter			= $this->params->get('socialthumbtwitter');
$bootscolumns				= $this->params->get('bootscolumns');

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

$bstheme					= $this->params->get('bstheme', 'auto');
	$bstheme = in_array(strtolower($bstheme), ['light','dark','auto'])
    ? strtolower($bstheme)
    : 'auto';

$headerfontfamily			= getGoogleFontFamily($headerfont, 'header', $headerfontname);
$bodyfontfamily				= getGoogleFontFamily($bodyfont, 'body', $bodyfontname);
$isheadergooglefont			= isGoogleFont($headerfont);
$isbodygooglefont			= isGoogleFont($bodyfont);

$containerClass				= $fluidcontainer ? 'container-fluid' : 'container';

?>
<!DOCTYPE html>

<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" data-bs-theme="<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>"
  <?php echo $dataThemeAttr . $dataEditingAttr; ?>>
	<head>
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
		?>
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
		
		<?php	//	Favicons
			if($loadfavicons == 1) : ?>
			<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/apple-touch-icon.png">
			<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/favicon-32x32.png">
			<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/favicon-16x16.png">
			<link rel="manifest" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/site.webmanifest">
			<link rel="mask-icon" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/safari-pinned-tab.svg" color="#5bbad5">
			<link rel="shortcut icon" href="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/favicon.ico">
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-config" content="<?php echo $this->baseurl ?>media/templates/site/<?php echo $this->template ?>/favicons/browserconfig.xml">
			<meta name="theme-color" content="#ffffff">
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
   	 	<meta name="HandheldFriendly" content="true">
		<meta name="apple-mobile-web-app-capable" content="YES">

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
		$doc = JFactory::getDocument();
		$pageTitle = $doc->getTitle();
		$metaDescription = $doc->getMetaData('description');
		
		// Get the full current page URL
		$currentPageURL = JUri::getInstance()->toString();
		
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

    	<jdoc:include type="styles" />
    			
		<?php	//	Load Bootstrap or Bootswatch theme.
			if($bootstrapsource == 1 || $bootstrapsource == 3) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/vendor/bootstrap/css/bootstrap.min.css">
				
			<?php elseif($bootstrapsource == 2) : ?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
			<?php elseif($bootstrapsource == 5) : ?>
				<?php echo $bootstrapcdn; ?>	
			<?php elseif($bootstrapsource == 6) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/cosmo/bootstrap.min.css" integrity="sha512-PU+mnI7iaSDt/G/adHVcQOX2I+K3bQ27kwHJQ1rPq5iqQvHuHSdJOUU/TmPcUsyUGrfAxK+Z4rnx/SL+qCmBNQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 7) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/flatly/bootstrap.min.css" integrity="sha512-qoT4KwnRpAQ9uczPsw7GunsNmhRnYwSlE2KRCUPRQHSkDuLulCtDXuC2P/P6oqr3M5hoGagUG9pgHDPkD2zCDA==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 8) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/minty/bootstrap.min.css" integrity="sha512-+/uDiVv4ZLhHnXToJFPwYK08kkG2et1rSuiitOl+0hdzmx2N6HQaMqm/7ORzr8dhco+cyGZCRxkXU7EMMYOLfQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 9) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/quartz/bootstrap.min.css" integrity="sha512-K+FEHZnRHFnQ6iahLNQUCHNpKDHkrYxHZmzFjOJteRPjBhjLmOgJgGJsIYBDOS1wYxcSVvAcfg3ZFpm6tnbhOA==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 10) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/spacelab/bootstrap.min.css" integrity="sha512-NWw8XgTx2tYA5oEZwBrmyH8bg37mh4bIjeV4OLWwqI3ipQ0ITyiWTFdG295uz2lMy+QO9DszNZwta4m4mJpnmw==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 11) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/yeti/bootstrap.min.css" integrity="sha512-Iwexq+Vk4qT5CCO6UdOTzOxJUB0eQxAWAfm6ytWws6MMcD6illgOw7QFjWoqqd3bQJS/EZPUR9nOACG7i5WMPQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 12) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/cyborg/bootstrap.rtl.min.css" integrity="sha512-5K0gDb7qGyr8RSmVR3OzYcJdWQiACT/kXkXdNSkThlPaotiKrfdHpXtQppHDi+fpQ7pFWvOtPnlXVKNLfNJSfg==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 13) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/darkly/bootstrap.min.css" integrity="sha512-HDszXqSUU0om4Yj5dZOUNmtwXGWDa5ppESlX98yzbBS+z+3HQ8a/7kcdI1dv+jKq+1V5b01eYurE7+yFjw6Rdg==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 14) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/slate/bootstrap.min.css" integrity="sha512-3EVe7TjxthzbTGfmRFr7zIvHjDWW7viFDgKOoTJ7S5IIrrKVN5rbPVjj0F7nT6rTyAkURnzwoujxlALvHoO9jw==" crossorigin="anonymous" referrerpolicy="no-referrer">
			<?php elseif($bootstrapsource == 15) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/superhero/bootstrap.rtl.min.css" integrity="sha512-Kghq6IhdiZiBhUVgmB2i5s3KfP6xP+Agc2Ez1AOR1yoOzDa5EAexQV94Qrv0zrbpeD8tbskZcjssvs2VzIWFvA==" crossorigin="anonymous" referrerpolicy="no-referrer">
		<?php endif; ?>
		
		<?php	//	Load Google Fonts
			if(($headerfont == 1) && ($headerfontname != null)) : ?>
			<?php echo $headergooglefont; ?>
		<?php endif; ?>
		<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<?php echo $bodygooglefont; ?>
		<?php endif; ?>
		
		<?php
			if ($headerfont != 2 || $bodyfont != 2 || isset($headerbackground) || isset($bootstrapsource)) {
				$style = '<style>';
				$style .= ':root {';
			
				// If $bootstrapsource is NOT 0 and $headerbackground is "rgba(0, 0, 0, 0)"
				if ($bootstrapsource !== 0 && $headerbackground === "rgba(0, 0, 0, 0)") {
					$style .= 'body header.sticky { background-color: var(--bs-body-bg); }';
				}
			
				// If $headerbackground is NOT the default transparent color, use the custom color
				if ($headerbackground !== "rgba(0, 0, 0, 0)") {
					$style .= '--atomic-header-background-color: ' . $headerbackground . ';';
				} 
				// If $bootstrapsource is 0 and $headerbackground is "rgba(0, 0, 0, 0)"
				else if ($bootstrapsource === 0) {
					$style .= '--atomic-header-background-color: rgba(0, 0, 0, 0);';
				}
			
				$style .= '--atomic-header-font: ' . ($headerfont != 2 ? $headerfontfamily : 'none') . ';';
			
				if ($bodyfont != 2) {
					$style .= '--atomic-body-font: ' . ($bodyfont != 0 ? $bodyfontfamily : 'var(--bs-body-font-family)') . ';';
				}
				$style .= '}';
				
				if ($feediting === 1) {
					$style .= 'html[data-editing="no"] div.icons { display: none !important; }';
				}
				$style .= '</style>';
			
				echo $style;
			}
			?>
		<?php	//	Load FontAwesome
		if($fontawesome == 1 || $fontawesome == 6) : ?>
			<link rel="stylesheet" href="media/system/css/joomla-fontawesome.min.css">
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php elseif($fontawesome == 3) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<?php elseif($fontawesome == 4 || $fontawesome == 5) : ?>
			<?php echo $fontawesomecdn; ?>
		<?php endif; ?>
		
		<?php	//	Load BS Icons
			if($loadbsicons == 1) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
		<?php endif; ?>
		
		<?php	//	Load the RTL CSS file.
			if($this->direction == 'rtl') : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/template_rtl.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS fixes for Joomla & Bootstrap 5.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/css/template.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
				<link rel="stylesheet" href="<?php echo $root ?>/templates/<?php echo $this->template ?>/css/custom.css" type="text/css">
		<?php endif; ?>
			
		<jdoc:include type="scripts" />

		<?php // Load jQuery ?>
		<?php if ($jqlibrary == 0) : ?>
			<script src="media/vendor/jquery/js/jquery.min.js"></script>
		<?php elseif ($jqlibrary == 1) : ?>
			<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 2) : ?>
			<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
		<?php elseif ($jqlibrary == 3) : ?>
			<?php echo $jquerycdn ?>
		<?php endif; ?>
		
		<script>var defaultTheme = '<?php echo htmlspecialchars($bstheme, ENT_QUOTES, 'UTF-8'); ?>';</script>
		<!-- <script>var defaultTheme = "<?php echo $bstheme; ?>";</script> -->
		
		<?php	//	Load BS Themeswitcher
			if($bsthemes == 1) : ?>
				<script src="<?php echo $root ?>/media/templates/site/<?php echo $this->template ?>/js/themeswitcher.min.js"></script>
		<?php endif; ?>
		
		<?php	//	Load custom local user JavaScript
			if($customjs == 1) : ?>
				<script src="<?php echo $root ?>/templates/<?php echo $this->template ?>/js/custom.js"></script>
		<?php endif; ?>
		
		<?php	//	Use Scroll Reveal
			if($scrollreveal == 1) : ?>
				<script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>
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
	
	<?php if ($bodymenu == 1) : ?>  
	  <body class="<?php echo $active->alias; ?>"<?php echo $dataUserAttr; ?>>
	<?php elseif ($bodymenu == 2) : ?>
	  <body id="<?php echo $active->alias; ?>"<?php echo $dataUserAttr; ?>>
	<?php elseif ($bodymenu == 3) : ?>
	  <body id="<?php echo $active->alias; ?>" class="<?php echo $active->alias; ?>"<?php echo $dataUserAttr; ?>>
	<?php elseif ($casspositions == 1) : ?>
	  <body class="cassiopeia"<?php echo $dataUserAttr; ?>>
	<?php else : ?>
	  <body class="site
		<?php echo $option . ' ' . $wrapper . ' view-' . $view
		  . ($itemid   ? ' itemid-' . $itemid   : '')
		  . ($pageclass? ' ' . $pageclass       : '')
		  . ($this->direction === 'rtl' ? ' rtl' : ''); ?>"
		<?php echo $dataUserAttr; ?>>
	<?php endif; ?>

	<?php	//	Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody; ?>	
	<?php endif; ?>

	<?php
		if ($sidebarmenu && $this->countModules('sidebar-menu')) :
			echo LayoutHelper::render('sidebar.offcanvas', [
				'direction' => $this->direction
			]);
		endif;
	?>

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
	// Do we have any header at all?
	$hasHeaderContent = $this->countModules('header', true)
		|| $this->countModules('topmenu', true)
		|| $this->countModules('search', true)
		|| $logo
		|| $sitetitle
		|| $sitedescription;
	
	if ($hasHeaderContent):
		// Sticky class?
		$headerClass = ($stickyhead == 1) ? 'sticky' : '';
	
		// Brand = logo OR title OR description
		$hasBrand = $logo || $sitetitle || $sitedescription;
	
		// Mobile menu independent
		$hasMobile  = $this->countModules('mobilemenu', true);
	?>
	<header<?= $headerClass ? ' class="' . $headerClass . '"' : '' ?>>
	  <div class="<?= $containerClass ?>">
	
		<?php if ($casspositions == 1): ?>
		  <jdoc:include type="modules" name="topbar"     style="none" />
		  <jdoc:include type="modules" name="below-top" style="none" />
		<?php endif; ?>
	
		<div class="header-main row align-items-center w-100">
		
		  <?php if ($hasMobile): ?>
			<div class="col-auto d-md-none">
			  <?= LayoutHelper::render('header.mobilemenu', ['containerClass' => $containerClass]); ?>
			</div>
		  <?php endif; ?>
	
		  <?php if ($hasBrand): ?>
			<div class="col-12 col-md-4 d-flex align-items-center flex-wrap">
			  <?php if ($logo): ?>
				<div id="logo" class="me-3">
				  <a href="<?= $this->baseurl ?>">
					<img src="<?= $this->baseurl . '/' . htmlspecialchars($logo) ?>"
						 alt="<?= htmlspecialchars($sitetitle ?: '') ?>" />
				  </a>
				</div>
			  <?php endif; ?>
	
			  <?php if ($sitetitle || $sitedescription): ?>
				<div class="site-info">
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
		  <?php endif; ?>
	
		  <?php
			// Right column: if there's a brand block, split 4/8; otherwise full width
			$rightCols = $hasBrand ? 'col-12 col-md-8' : 'col-12';
		  ?>
		  <div class="<?= $rightCols ?> d-flex justify-content-end align-items-center">
			<?php if ($this->countModules('header', true)): ?>
			  <jdoc:include type="modules" name="header" style="none" />
			<?php endif; ?>
	
			<?php if (!empty($bsthemes)): ?>
			  <?= LayoutHelper::render('header.styleswitcher', ['bsthemes' => $bsthemes]); ?>
			<?php endif; ?>
		  </div>
		</div>
	
		<?php if ($this->countModules('topmenu', true)):
		  $navCols = $this->countModules('search', true) ? 'col-12 col-md-9' : 'col-12';
		?>
		  <div class="header-bottom row align-items-center">
			<div class="<?= $navCols ?>">
			  <nav class="navigation">
				<div class="nav-collapse">
				  <jdoc:include type="modules" name="topmenu" style="none" />
				  <?php if ($casspositions == 1): ?>
					<jdoc:include type="modules" name="menu" style="none" />
				  <?php endif; ?>
				</div>
			  </nav>
			</div>
	
			<?php if ($this->countModules('search', true)): ?>
			  <div class="col-12 col-md-3">
				<jdoc:include type="modules" name="search" style="none" />
			  </div>
			<?php endif; ?>
		  </div>
		<?php endif; ?>
	
	  </div>
	</header>
	<?php endif; ?>

	<main>
	  <div class="<?php echo $containerClass; ?>">
	
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
	
		  $columnOptions = [
			[2, 8, 2],
			[2, 7, 3],
			[2, 6, 4],
			[3, 6, 3],
			[4, 4, 4],
		  ];
		  $choice = isset($bootscolumns) && array_key_exists((int)$bootscolumns, $columnOptions)
				  ? (int)$bootscolumns
				  : 0;
	
		  if ($showLeft && $showRight) {
			list($l, $m, $r) = $columnOptions[$choice];
			// left sidebar hidden until LG, then “l” cols
			$leftClass  = "d-none d-lg-block col-lg-{$l}";
			$mainClass  = "col-12 col-lg-{$m}";
			$rightClass = "col-12 col-lg-{$r}";
		  }
		  elseif ($showLeft) {
			// only left: hide on small, show 3 cols at LG
			$leftClass = "d-none d-lg-block col-lg-3";
			$mainClass = "col-12 col-lg-9";
		  }
		  elseif ($showRight) {
			$mainClass  = "col-12 col-lg-9";
			$rightClass = "col-12 col-lg-3";
		  }
		  else {
			$mainClass = "col-12";
		  }
		?>
	
		<div class="row">
		  <?php if ($showLeft) : ?>
			<div class="container-sidebar-left mt-4 <?= $leftClass; ?>">
			  <jdoc:include type="modules" name="sidebar-left" style="card" />
			  <jdoc:include type="modules" name="leftbody"     style="card" />
			</div>
		  <?php endif; ?>
	
		  <div class="container-component mt-4 <?= $mainClass; ?>">
			<jdoc:include type="modules" name="breadcrumbs" style="none" />
			<jdoc:include type="modules" name="abovebody"   style="none" />
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="main-top"  style="card" />
			<?php endif; ?>
			<jdoc:include type="message" />
			<jdoc:include type="component" />
			<jdoc:include type="modules" name="belowbody"   style="none" />
			<?php if ($casspositions == 1) : ?>
			  <jdoc:include type="modules" name="main-bottom" style="card" />
			<?php endif; ?>
		  </div>
	
		  <?php if ($showRight) : ?>
			<div class="container-sidebar-right mt-4 <?= $rightClass; ?>">
			  <jdoc:include type="modules" name="sidebar-right" style="card" />
			  <jdoc:include type="modules" name="rightbody"      style="card" />
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



	<?php if ($this->countModules('footer', true) or $copyright == 1) : ?>
		<footer>
			<div class="<?php echo $containerClass; ?>">
			<div class="row">
				<jdoc:include type="modules" name="footer" title="Footer" style="none" />
				<?php	//	Copyright
					if($copyright == 1) : ?>
					<div class="copyright">
						<?php if(($copyrighttxt != null) && ($copyright == 1)) : ?>
						&copy;<?php echo date('Y'); ?> <?php echo $copyrighttxt ?>
						<?php else : ?>
						&copy;<?php echo date('Y'); ?> <?php echo htmlspecialchars($app->getCfg('sitename')); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</footer>
		<?php endif; ?>
		
		<?php	//	Load Bootstrap JS
			if($bootstrapsource == 1) : ?>
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 2) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource >= 6 && $bootstrapsource <= 15) : ?>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			<?php elseif($bootstrapsource == 3) : ?>
				<?php HTMLHelper::_('bootstrap.framework'); ?>
			<?php endif; ?>
			
		<?php	//	Use Instant.page
			if($instant == 1) : ?>
			<script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipYXnSU0ygqeac2q7CVYMbh84q0uHVRRxEtvFPiQYbXWUorga2aqZJ0z"></script>
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
