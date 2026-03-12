<?php
/**
 * @copyright	Copyright (C) 2008-2025 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
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
$app = JFactory::getApplication();
$user = JFactory::getUser();

//	Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

//	Get Joomla template variables
$this->baseurl = JUri::base();
$option		= $input->getCmd('option', '');
$view			= $input->getCmd('view', '');
$itemid		= $input->getCmd('Itemid', '');
$menu			= $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

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
		// Combine conditions using logical OR (||) for efficiency
		if ($isheadergooglefont || $isbodygooglefont) {
			// Preconnect links once (outside nested conditionals)
			echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
			echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';

			// Font links based on combined conditions
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
					
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
   	 	<meta name="HandheldFriendly" content="true">
		<meta name="apple-mobile-web-app-capable" content="YES">

		<?php
		
		// Get Joomla’s dynamic page title and meta description
		$doc = JFactory::getDocument();
		$pageTitle = $doc->getTitle();
		$metaDescription = $doc->getMetaData('description');
		
		// Get the full current page URL
		$currentPageURL = JUri::getInstance()->toString();
		
		// Base URL from template (for cleaning relative image URLs)
		$baseurl = $this->baseurl;

		?>
		
    	<jdoc:include type="styles" />
    			
		<?php	//	Load Bootstrap or Bootswatch theme.
			if($bootstrapsource == 1 || $bootstrapsource == 3) : ?>
				<link rel="stylesheet" href="media/vendor/bootstrap/css/bootstrap.min.css">
				
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
				<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/template_rtl.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS fixes for Joomla & Bootstrap 5.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/template.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
				<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/custom.css" type="text/css">
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
				<script src="/templates/<?php echo $this->template ?>/js/themeswitcher.min.js"></script>
		<?php endif; ?>
		
		<?php	//	Load custom local user JavaScript
			if($customjs == 1) : ?>
				<script src="/templates/<?php echo $this->template ?>/js/custom.js"></script>
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
	  <body class="contentpane component 
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
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 2) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource >= 6 && $bootstrapsource <= 15) : ?>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			<?php elseif($bootstrapsource == 3) : ?>
				<?php HTMLHelper::_('bootstrap.framework'); ?>
			<?php endif; ?>
		
		<?php	//	Add custom code before closing body tag
			if($codebeforebody != null) : ?>
			<?php echo $codebeforebody; ?>	
		<?php endif; ?>
		
	</body>
</html>
