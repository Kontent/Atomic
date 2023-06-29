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

//	Assign template params
$bodyfont							= $this->params->get('bodyfont');
$bodyfontname					= $this->params->get('bodyfontname');
$bodymenu							= $this->params->get('bodymenu');
$bodygooglefont					= $this->params->get('bodygooglefont');
$bootstrapcdn						= $this->params->get('bootstrapcdn');
$bootstrapsource				= $this->params->get('bootstrapsource');
$bsfixjoomla						= $this->params->get('bsfixjoomla');
$bsicons								= $this->params->get('bsicons');
$codeafterbody					= $this->params->get('codeafterbody');
$codeafterhead					= $this->params->get('codeafterhead');
$codebeforebody				= $this->params->get('codebeforebody');
$codebeforehead				= $this->params->get('codebeforehead');
$copyright							= $this->params->get('copyright');
$copyrighttxt						= $this->params->get('copyrighttxt');
$customcssfile					= $this->params->get('customcssfile');
$customjs							= $this->params->get('customjs');
$fluidcontainer					= $this->params->get('fluidcontainer');
$fontawesome						= $this->params->get('fontawesome');
$fontawesomecdn				= $this->params->get('fontawesomecdn');
$gacode								= $this->params->get('gacode');
$headerfont							= $this->params->get('headerfont');
$headergooglefont				= $this->params->get('headergooglefont');
$headerfontname				= $this->params->get('headerfontname');
$instant								= $this->params->get('instant');
$jqlibrary								= $this->params->get('jqlibrary');
$jquerycdn							= $this->params->get('jquerycdn');
$killgenerator						= $this->params->get('killgenerator');
$loadfavicons						= $this->params->get('loadfavicons');
$loadbsicons						= $this->params->get('loadbsicons');
$logo									= $this->params->get('logo');
$scrollreveal						= $this->params->get('scrollreveal');
$sitedescription					= $this->params->get('sitedescription');
$sitetitle								= $this->params->get('sitetitle');
$casspositions					= $this->params->get('casspositions');


// Register assets
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wr = $wa->getRegistry();

// Enable assets
// $wa->useStyle('template.atomic.bs5css');
// $wa->useStyle('template.atomic.css');
// $wa->useScript('template.atomic.js');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
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
			<link rel="apple-touch-icon" sizes="180x180" href="templates/atomic/favicons/apple-touch-icon.png">
			<link rel="icon" type="image/png" sizes="32x32" href="templates/atomic/favicons/favicon-32x32.png">
			<link rel="icon" type="image/png" sizes="16x16" href="templates/atomic/favicons/favicon-16x16.png">
			<link rel="manifest" href="templates/atomic/favicons/site.webmanifest">
			<link rel="mask-icon" href="templates/atomic/favicons/safari-pinned-tab.svg" color="#5bbad5">
			<link rel="shortcut icon" href="templates/atomic/favicons/favicon.ico">
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-config" content="templates/atomic/favicons/browserconfig.xml">
			<meta name="theme-color" content="#ffffff">
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		
    	<jdoc:include type="styles" />
    			
		<?php	//	Load Bootstrap or Bootswatch theme.
			if($bootstrapsource == 1 || $bootstrapsource == 2) : ?>
				<link rel="stylesheet" href="media/vendor/bootstrap/css/bootstrap.min.css" />
				
			<?php elseif($bootstrapsource == 3 || $bootstrapsource == 4) : ?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
			
			<?php elseif($bootstrapsource == 5) : ?>
				<?php echo $bootstrapcdn; ?>	
		
			<?php elseif($bootstrapsource == 6) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/cosmo/bootstrap.min.css" integrity="sha512-NLsflIDF3KkL2Ne9b7dYTkJ2/EDCe8Uo+/uat0QDDvqFOroaiZnIok2p7OHMt/iUIdQF8HVmcuuyvgzvOfU0mA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 7) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/flatly/bootstrap.min.css" integrity="sha512-N/JRw8RFoUkWgQIpunoKtmZShzrHbs724xV4DMh+LSNjebmrgNy2dzAIUhoOqSazEZ/bLlulWy2muCxletfrsA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 8) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/minty/bootstrap.min.css" integrity="sha512-NXJDZEor7THMyky4W3o7oqua70wf7v7YDp+UfJxCLA6DJZIxksgnnpd+kQF0GsT1Mmw/VxsTGpnJcC1yapOEfw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 9) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/quartz/bootstrap.min.css" integrity="sha512-Wwa32vy+l32AJgZBv6XABgPyi254CmzXkvyZh+EaQWCyaoLOP4BIG00Oz8TbiRutO8VMi9VHJuy7AbFqDkF9Hw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 10) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/spacelab/bootstrap.min.css" integrity="sha512-G5YYZX20vJvDg4huNhBOtxBSQTlonPPFTLvy2cY7F6AJ9SYNDXevQUE5O8wlHyVY1/LTt+Quk6WQ9ObbwJ3hOw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 11) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/yeti/bootstrap.min.css" integrity="sha512-VzKni1jm9BXG761rJfi0f0Vxd2B6NGWFrpxRZlnTVnUt4HvVlpSq8jQr2J3UVH7FoglUqpLhJIAq96ulIBPbbg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 12) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/cyborg/bootstrap.min.css" integrity="sha512-jwIqEv8o/kTBMJVtbNCBrDqhBojl0YSUam+EFpLjVOC86Ci6t4ZciTnIkelFNOik+dEQVymKGcQLiaJZNAfWRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 13) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/darkly/bootstrap.min.css" integrity="sha512-3xynESL0QF3ERUl9se1VJk043nWT+UzWJveifBw7kLtC226vyGINZFtmyK015F83KBSNW+67alYSY2cCj1LHOQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 14) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/slate/bootstrap.min.css" integrity="sha512-TSKJb47lA8eqhl9ChXTWG686i5y81CiunKEv24OsK278WJoFov2ZOf/yFTgeb3mcu1ElkNA6R3iDZH+WHbOIWw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 15) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/superhero/bootstrap.min.css" integrity="sha512-J0q3uYNPOZ/n9hYguPcu6W6vWeLg1auyzcHnpQ4T/yd57kHb2tCz4MpVBdvoJwWXOUjDWT9t9QdsTiMrS2XRvw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php endif; ?>
		
		<?php	//	Load Google Fonts
			if(($headerfont == 1) && ($headerfontname != null)) : ?>
			<?php echo $headergooglefont; ?>
		<?php endif; ?>
		<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<?php echo $bodygooglefont; ?>
		<?php endif; ?>
		
		<style>
			:root {
				<?php	//	Define CSS variables
					if(($headerfont == 1) && ($headerfontname != null)) : ?>
					--atomic-header-font: <?php echo $headerfontname; ?>;
				<?php endif; ?>
				<?php if(($bodyfont == 1) && ($bodyfontname != null)) : ?>
				--atomic-body-font: <?php echo $bodyfontname; ?>;
				<?php endif; ?>
			}
		</style>
				
		<?php	//	Load FontAwesome
		if($fontawesome == 1) : ?>
			<link rel="stylesheet" href="media/vendor/fontawesome-free/css/fontawesome.min.css" />
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<?php elseif($fontawesome == 3) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<?php elseif($fontawesome == 4 || $fontawesome == 5) : ?>
			<?php echo $fontawesomecdn; ?>
		<?php endif; ?>
		
		<?php	//	Load BS Icons
			if($loadbsicons == 1) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.min.css" type="text/css">
		<?php endif; ?>
			
		<jdoc:include type="scripts" />
			
		<?php	//	Load jQuery	?>
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<script src="media/vendor/jquery/js/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 3)) : ?>
			<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
		<?php else : ?>
			<script src="https://<?php echo $jquerycdn ?>"></script>
		<?php endif; ?>
		
		<?php	//	Load custom local user JavaScript
			if($customjs == 1) : ?>
				<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
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
	
	<?php	//	Mobile menu
		if ($this->countModules( 'mobilemenu' )) : ?>
		<jdoc:include type="modules" name="mobilemenu" style="mobilemenu" />
	<?php endif; ?>
	
	<header>
		
		<?php if($casspositions == 1) : ?>
			<jdoc:include type="modules" name="topbar" style="none" />
			<jdoc:include type="modules" name="below-top" style="none" />
		<?php endif; ?>
		
		<?php	if($sitedescription != null || $sitetitle != null || $logo != null) : ?>		
			<div class="container-header">
				
				<?php	if($alert != null || $styleswitcher != null) : ?>
				<div class="alertbar">
					<?php if ($this->countModules('alert')) : ?>
						<jdoc:include type="modules" name="alert" style="basic" />
					<?php endif; ?>
					<?php if ($this->countModules('styleswitcher')) : ?>
						<jdoc:include type="modules" name="styleswitcher" style="basic" />
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
			</div>
		<?php endif; ?>
	
		<?php if ($this->countModules('topmenu')) : ?>
			<div class="row">
				<nav class="navigation">
					<div class="nav-collapse">
						<jdoc:include type="modules" name="navigation" style="basic" />
						<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="menu" style="none" /><?php endif; ?>
					</div>
				</nav>
				<div class="col-md-3">
					<jdoc:include type="modules" name="search" style="none" />
				</div>
			</div>
		<?php endif; ?>
	</header>

    <main>
		<div class="container-main">
			<?php if ($this->countModules('leftbody', true)) : ?>
				<div class="container-sidebar-left">
					<jdoc:include type="modules" name="leftbody"  style="default" />
					<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="sidebar-left" style="card" /><?php endif; ?>
				</div>
			<?php endif; ?>
	
			<div class="container-component">
				<jdoc:include type="modules" name="breadcrumbs" style="none" />
				<jdoc:include type="modules" name="abovebody" style="none" />
				<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="main-top" style="card" /><?php endif; ?>
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<jdoc:include type="modules" name="belowbody" style="default" />
				<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="main-bottom" style="card" /><?php endif; ?>
			</div>

			<?php if ($this->countModules('rightbody', true)) : ?>
				<div class="container-sidebar-right">
					<jdoc:include type="modules" name="rightbody" style="card" />
					<?php if($casspositions == 1) : ?><jdoc:include type="modules" name="sidebar-right" style="card" /><?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</main>

	<?php if ($this->countModules('footer', true) or $copyright == 1) : ?>
		<footer class="container">
			<div class="row">
			<jdoc:include type="modules" name="footer" style="none" />
			<?php	//	Copyright
				if($copyright == 1) : ?>
				<hr />
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
		
		<?php	//	Load Bootstrap JS
			if($bootstrapsource == 1) : ?>
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 2) : ?>
				<script src="media/vendor/bootstrap/js/popper.min.js"></script>
				<script src="media/vendor/bootstrap/js/bootstrap-es5.min.js"></script>
			<?php elseif($bootstrapsource == 3) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource == 4) : ?>
				<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
			<?php elseif($bootstrapsource >= 6 && $bootstrapsource <= 15) : ?>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
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
