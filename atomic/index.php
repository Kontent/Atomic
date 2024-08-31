<?php
/**
 * @copyright	Copyright (C) 2008-2023 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
$wa = $this->getWebAssetManager();

//	Get the application object for things like displaying the site name 
$app = JFactory::getApplication();
$user = JFactory::getUser();

//	Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

$version_parts = explode('.', JVERSION);
$isJ5 = $version_parts[0] === '5';
$isJ4 = $version_parts[0] === '4';
$isJ3 = $version_parts[0] === '3';

//	Assign template params
$bodyfont			= $this->params->get('bodyfont');
$bodyfontname		= $this->params->get('bodyfontname');
$bodymenu			= $this->params->get('bodymenu');
$bodygooglefont		= $this->params->get('bodygooglefont');
$bootstrapcdn		= $this->params->get('bootstrapcdn');
$bootstrapsource	= $this->params->get('bootstrapsource');
$bsfixjoomla		= $this->params->get('bsfixjoomla');
$bsicons			= $this->params->get('bsicons');
$bstheme			= $this->params->get('bstheme');
$bsthemes			= $this->params->get('bsthemes');
$codeafterbody		= $this->params->get('codeafterbody');
$codeafterhead		= $this->params->get('codeafterhead');
$codebeforebody		= $this->params->get('codebeforebody');
$codebeforehead		= $this->params->get('codebeforehead');
$copyright			= $this->params->get('copyright');
$copyrighttxt		= $this->params->get('copyrighttxt');
$customcssfile		= $this->params->get('customcssfile');
$customjs			= $this->params->get('customjs');
$fluidcontainer		= $this->params->get('fluidcontainer');
$fontawesome		= $this->params->get('fontawesome');
$fontawesomecdn		= $this->params->get('fontawesomecdn');
$gacode				= $this->params->get('gacode');
$headerfont			= $this->params->get('headerfont');
$headergooglefont	= $this->params->get('headergooglefont');
$headerfontname		= $this->params->get('headerfontname');
$instant			= $this->params->get('instant');
$jqlibrary			= $this->params->get('jqlibrary');
$jquerycdn			= $this->params->get('jquerycdn');
$killgenerator		= $this->params->get('killgenerator');
$loadfavicons		= $this->params->get('loadfavicons');
$loadbsicons		= $this->params->get('loadbsicons');
$logo				= $this->params->get('logo');
$scrollreveal		= $this->params->get('scrollreveal');
$sitedescription	= $this->params->get('sitedescription');
$sitetitle			= $this->params->get('sitetitle');
$casspositions		= $this->params->get('casspositions');
$stickyhead			= $this->params->get('stickyhead');
$loadbsthemes		= $this->params->get('bsthemes');
$sidebarmenu		= $this->params->get('sidebarmenu');
$sidebarmenutitle	= $this->params->get('sidebarmenutitle');
$sidebarmenupos		= $this->params->get('sidebarmenupos');

// Register assets
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wr = $wa->getRegistry();

// Enable assets
// $wa->useStyle('template.atomic.bs5css');
// $wa->useStyle('template.atomic.css');
// $wa->useScript('template.atomic.js');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" data-bs-theme="<?php echo $bstheme; ?>">
	<head>
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
			<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon.png">
			<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon-32x32.png">
			<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon-16x16.png">
			<link rel="manifest" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/site.webmanifest">
			<link rel="mask-icon" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/safari-pinned-tab.svg" color="#5bbad5">
			<link rel="shortcut icon" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon.ico">
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-config" content="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/browserconfig.xml">
			<meta name="theme-color" content="#ffffff">
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
   	 	<meta name="HandheldFriendly" content="true">
		<meta name="apple-mobile-web-app-capable" content="YES">
		
    	<jdoc:include type="styles" />
    			
		<?php	//	Load Bootstrap or Bootswatch theme.
			if($bootstrapsource == 1 || $bootstrapsource == 2 || $bootstrapsource == 16) : ?>
				<link rel="stylesheet" href="media/vendor/bootstrap/css/bootstrap.min.css">
				
			<?php elseif($bootstrapsource == 3 || $bootstrapsource == 4) : ?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
			
			<?php elseif($bootstrapsource == 5) : ?>
				<?php echo $bootstrapcdn; ?>	
		
			<?php elseif($bootstrapsource == 6) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/cosmo/bootstrap.min.css" integrity="sha512-PU+mnI7iaSDt/G/adHVcQOX2I+K3bQ27kwHJQ1rPq5iqQvHuHSdJOUU/TmPcUsyUGrfAxK+Z4rnx/SL+qCmBNQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 7) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/flatly/bootstrap.min.css" integrity="sha512-qoT4KwnRpAQ9uczPsw7GunsNmhRnYwSlE2KRCUPRQHSkDuLulCtDXuC2P/P6oqr3M5hoGagUG9pgHDPkD2zCDA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 8) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/minty/bootstrap.min.css" integrity="sha512-+/uDiVv4ZLhHnXToJFPwYK08kkG2et1rSuiitOl+0hdzmx2N6HQaMqm/7ORzr8dhco+cyGZCRxkXU7EMMYOLfQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 9) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/quartz/bootstrap.min.css" integrity="sha512-K+FEHZnRHFnQ6iahLNQUCHNpKDHkrYxHZmzFjOJteRPjBhjLmOgJgGJsIYBDOS1wYxcSVvAcfg3ZFpm6tnbhOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 10) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/spacelab/bootstrap.min.css" integrity="sha512-NWw8XgTx2tYA5oEZwBrmyH8bg37mh4bIjeV4OLWwqI3ipQ0ITyiWTFdG295uz2lMy+QO9DszNZwta4m4mJpnmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 11) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/yeti/bootstrap.min.css" integrity="sha512-Iwexq+Vk4qT5CCO6UdOTzOxJUB0eQxAWAfm6ytWws6MMcD6illgOw7QFjWoqqd3bQJS/EZPUR9nOACG7i5WMPQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 12) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/cyborg/bootstrap.min.css" integrity="sha512-M+Wrv9LTvQe81gFD2ZE3xxPTN5V2n1iLCXsldIxXvfs6tP+6VihBCwCMBkkjkQUZVmEHBsowb9Vqsq1et1teEg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 13) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/darkly/bootstrap.min.css" integrity="sha512-HDszXqSUU0om4Yj5dZOUNmtwXGWDa5ppESlX98yzbBS+z+3HQ8a/7kcdI1dv+jKq+1V5b01eYurE7+yFjw6Rdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 14) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/slate/bootstrap.min.css" integrity="sha512-8n7mJPYc1PYv0QSKTgmWUNAXc3ivx3bf1m2Pb/Dn+StJ8D69Hyxwq+aMw6NUreHzSMlwB6PqT5JBiDgUCyjIpg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<?php elseif($bootstrapsource == 15) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/superhero/bootstrap.min.css" integrity="sha512-yeFVFyLRIY48erNjFZ1uXiERPXN8izq4mBNe4iSgVYT0bq/ZiSsWwTlaSObBDeqR/+7MBw1g23iSpI9ru/qtGQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php endif; ?>
		
		<?php	//	Load Google Fonts
			if(($headerfont == 1) && ($headerfontname != null)) : ?>
			<?php echo $headergooglefont; ?>
		<?php endif; ?>
		<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<?php echo $bodygooglefont; ?>
		<?php endif; ?>
		
		<?php if(($headerfont != 2) || ($bodyfont != 2)) : ?>
			<style>
			:root {
			<?php	//	Define CSS variables
						if(($headerfont == 1) && ($headerfontname != null)) : ?>
						--atomic-header-font: <?php echo $headerfontname; ?>;
					<?php endif; ?>
					
					<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
						--atomic-body-font: <?php echo $bodyfontname; ?>;
						--bs-body-font-family: var(--atomic-body-font);
						--bs-btn-font-family: var(--atomic-body-font);
					<?php else : ?>
						--atomic-body-font: var(--bs-body-font-family);
						--bs-btn-font-family: var(--bs-body-font-family);
					<?php endif; ?>
			}
			</style>
		<?php endif; ?>
				
		<?php	//	Load FontAwesome
		if($fontawesome == 1) : ?>
			<link rel="stylesheet" href="media/system/css/joomla-fontawesome.min.css">
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php elseif($fontawesome == 3) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<?php elseif($fontawesome == 4 || $fontawesome == 5) : ?>
			<?php echo $fontawesomecdn; ?>
		<?php endif; ?>
		
		<?php	//	Load BS Icons
			if($loadbsicons == 1) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
		<?php endif; ?>
		
		<?php	//	Load the RTL CSS file.
			if($this->direction == 'rtl') : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS fixes for Joomla & Bootstrap 5.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs5.min.css" type="text/css">
		<?php endif; ?>
		
		<?php	//	Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>
			
		<jdoc:include type="scripts" />
			
		<?php	//	Load jQuery	?>
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<script src="media/vendor/jquery/js/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 3)) : ?>
			<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 4)) : ?>
			<?php echo $jquerycdn ?>
		<?php else : ?>
		<?php endif; ?>
		
		<?php	//	Load BS Styleswitcher
			if($loadbsthemes == 1) : ?>
				<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/theme.js"></script>
		<?php endif; ?>
		
		<?php	//	Load custom local user JavaScript
			if($customjs == 1) : ?>
				<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
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
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gacode; ?>" type="module"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<?php echo $gacode; ?>');
		</script>
		<?php endif; ?>
	</head>

	<?php	//	Add the menu item alias to the body ID, class, or both. ?>
	<?php if($bodymenu == 1) : ?>	
		<body class="<?php echo $active->alias; ?> ">
	<?php elseif($bodymenu == 2) : ?>
		<body id="<?php echo $active->alias; ?>">
	<?php elseif($bodymenu == 3) : ?>
		<body id="<?php echo $active->alias; ?>" class="<?php echo $active->alias; ?> ">
	<?php elseif($casspositions == 1) : ?>
		<body class="cassiopeia">
	<?php else : ?>
		<body>
	<?php endif; ?>
		
	<?php	//	Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody; ?>	
	<?php endif; ?>


	<?php	//	Fixed or fluid width container
		if($fluidcontainer == 1) : ?>
		<div class="container-fluid">
	<?php else : ?>
		<div class="container">
	<?php endif; ?>
	
	<?php if ($this->countModules( 'mobilemenu' )) : ?>
		<jdoc:include type="modules" name="mobilemenu" style="mobilemenu" />
	<?php endif; ?>
	
	<?php if($sidebarmenu): ?>
		<div class="offcanvas offcanvas-<?php echo $sidebarmenupos ?: 'start'; ?>" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1" id="offcanvas<?php echo ucfirst($sidebarmenupos) ?: 'Start'; ?>" aria-labelledby="offcanvas<?php echo ucfirst($sidebarmenupos) ?: 'Start'; ?>Label">
			<button class="btn btn-primary offcanvas-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas<?php echo ucfirst($sidebarmenupos) ?: 'Start'; ?>" aria-controls="offcanvas<?php echo ucfirst($sidebarmenupos) ?: 'Start'; ?>">
				<span class="offcanvas-toggle-icon offcanvas-toggle-icon--close"><i class="fas fa-times"></i></span>
				<span class="offcanvas-toggle-icon offcanvas-toggle-icon--open"><i class="fas fa-bars"></i></span>
			</button>
			<div class="offcanvas-content d-flex flex-column">
				<div class="offcanvas-header">
					<h5 class="offcanvas-title"><?php echo $sidebarmenutitle ?: 'Menu'; ?></h5>
				</div>
				<div class="offcanvas-body">
				<?php if ($this->countModules( 'sidebar-menu' )) : ?>
					<jdoc:include type="modules" name="sidebar-menu" style="none" />
				<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php	//	Hide header if nothing in it
	if ($this->countModules('alert', true) 
	or ($this->countModules('header', true)) 
	or ($this->countModules('topmenu', true)) 
	or ($this->countModules('search', true)) 
	or ($sitedescription != null || $sitetitle != null || $logo != null)) : ?>
	
	<?php	//	Make sticky or not ?>
	<?php if($stickyhead == 1) : ?>	
		<header class="sticky">
	<?php else : ?>
		<header>
	<?php endif; ?>
	
		<?php if($casspositions == 1) : ?>
			<jdoc:include type="modules" name="topbar" style="none" />
			<jdoc:include type="modules" name="below-top" style="none" />
		<?php endif; ?>
		
		<div class="row">
			<div class="container-header">
				
				<?php if ($this->countModules('alert', true) or ($loadbsthemes == 1)) : ?>
				<div class="alertbar">
					<?php if ($this->countModules('alert')) : ?>
						<jdoc:include type="modules" name="alert" style="none" />
					<?php endif; ?>
					
					<?php	//	Load BS Styleswitcher
						if($loadbsthemes == 1) : ?>
						<div><a id="themeBtn" href="#"class="nav-link"><i class=""></i></a></div>
					<?php endif; ?>
					
				</div>
				<?php endif; ?>
				
				<?php if($logo != null) : ?>
					<span id="logo">
						<a href="<?php echo $this->baseurl; ?>"><img src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($this->params->get('sitetitle')); ?>" /></a>
					</span>
				<?php endif; ?>
				
				<?php	if($sitedescription != null || $sitetitle != null) : ?>
				<div class="container-title">
					<?php if($sitetitle != null) : ?>
						<h1><?php echo $sitetitle; ?></h1>
					<?php endif; ?>
					<?php if($sitedescription != null) : ?>
						<h3><?php echo $sitedescription; ?></h3>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<?php if ($this->countModules('header')) : ?>
					<jdoc:include type="modules" name="header" style="none" />
				<?php endif; ?>
				
			</div>
		</div>

		<?php if ($this->countModules('topmenu')) : ?>
			<div class="row">
				<?php if ($this->countModules('search')) : ?>
					<div class="col-md-9">
					<?php endif; ?>
						<nav class="navigation">
							<div class="nav-collapse">
								<jdoc:include type="modules" name="<?php echo $casspositions == 1 ? 'menu' : 'topmenu'; ?>" style="none" />
							</div>
						</nav>
					<?php if ($this->countModules('search')) : ?>
					</div>
					<div class="col-md-3">
						<jdoc:include type="modules" name="search" style="none" />
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</header>
	<?php endif; ?>
		
    <main>
    	<?php if($casspositions == 1) : ?>
    	<div class="row">
    		<div class="row">
    			<jdoc:include type="modules" name="banner" style="none" />
    		</div>
			<jdoc:include type="modules" name="top-a" style="none" />
			<jdoc:include type="modules" name="top-b" style="none" />
		</div>
		<?php endif; ?>
    	
    	<div class="row">
			<div class="container-main">
				<?php if ($this->countModules('leftbody', true)) : ?>
					<div class="container-sidebar-left">
						<jdoc:include type="modules" name="leftbody"  style="none" />
						<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="sidebar-left" style="card" /><?php endif; ?>
					</div>
				<?php endif; ?>
	
				<div class="container-component">
					<jdoc:include type="modules" name="breadcrumbs" style="none" />
					<jdoc:include type="modules" name="abovebody" style="none" />
					<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="main-top" style="card" /><?php endif; ?>
					<jdoc:include type="message" />
					<jdoc:include type="component" />
					<jdoc:include type="modules" name="belowbody" style="none" />
					<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="main-bottom" style="card" /><?php endif; ?>
				</div>

				<?php if ($this->countModules('rightbody', true)) : ?>
					<div class="container-sidebar-right">
						<jdoc:include type="modules" name="rightbody" style="none" />
						<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="sidebar-right" style="card" /><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<?php if($casspositions == 1) : ?>
    	<div class="row">
			<jdoc:include type="modules" name="bottom-a" style="none" />
			<jdoc:include type="modules" name="bottom-b" style="none" />
		</div>
		<?php endif; ?>		
	</main>

	<?php if ($this->countModules('footer', true) or $copyright == 1) : ?>
		<footer>
			<div class="row">
				<jdoc:include type="modules" name="footer" style="none" />
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
				</footer>
			<?php endif; ?>
			</div>
		</div>
		
		<?php	//	Load Bootstrap JS
			if($bootstrapsource == 1) : ?>
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 2) : ?>
				<script src="media/vendor/bootstrap/js/popper.min.js"></script>
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 3) : ?>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			<?php elseif($bootstrapsource == 4) : ?>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			<?php elseif($bootstrapsource >= 6 && $bootstrapsource <= 15) : ?>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			<?php elseif($bootstrapsource == 16) : ?>
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
		
		<jdoc:include type="modules" name="debug" style="none" />
	</body>
</html>
