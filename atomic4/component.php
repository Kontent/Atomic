<?php
/**
 * @copyright	Copyright (C) 2019 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Get the application object for things like displaying the site name 
$app  = JFactory::getApplication();
$user = JFactory::getUser();

// Output as HTML5
$this->setHtml5(true);

// Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

// Get params from template
$params = $app->getTemplate(true)->params;

// Assign template params
$logo								= $this->params->get('logo');
$sitetitle							= $this->params->get('sitetitle');
$sitedescription					= $this->params->get('sitedescription');
$bodymenu							= $this->params->get('bodymenu');
$bootstrapsource					= $this->params->get('bootstrapsource');
$bootstrapcdn						= $this->params->get('bootstrapcdn');
$fontawesome						= $this->params->get('fontawesome');
$customcsscode						= $this->params->get('customcsscode');
$customcssfile						= $this->params->get('customcssfile');
$customjs							= $this->params->get('customjs');
$fluidcontainer						= $this->params->get('fluidcontainer');
$jqlibrary							= $this->params->get('jqlibrary');
$jquerycdn							= $this->params->get('jquerycdn');
$bsfixjoomla						= $this->params->get('bsfixjoomla');
$gacode								= $this->params->get('gacode');
$pageheader							= $this->params->get('pageheader');
$topmenu							= $this->params->get('topmenu');
$abovebody							= $this->params->get('abovebody');
$leftbody							= $this->params->get('leftbody');
$rightbody							= $this->params->get('rightbody');
$belowbody							= $this->params->get('belowbody');
$footer								= $this->params->get('footer');
$headerfont							= $this->params->get('headerfont');
$headerfontname						= $this->params->get('headerfontname');
$bodyfont							= $this->params->get('bodyfont');
$bodyfontname						= $this->params->get('bodyfontname');
$killjoomlajs						= $this->params->get('killjoomlajs');
$killjoomlacss						= $this->params->get('killjoomlacss');
$killgenerator						= $this->params->get('killgenerator');
$copyright							= $this->params->get('copyright');
$copyrighttxt						= $this->params->get('copyrighttxt');
$noconflict							= $this->params->get('noconflict');
$jqmigrate							= $this->params->get('jqmigrate');
$loadfavicons						= $this->params->get('loadfavicons');
$codeafterhead						= $this->params->get('codeafterhead');
$codebeforehead						= $this->params->get('codebeforehead');
$codeafterbody						= $this->params->get('codeafterbody');
$codebeforebody						= $this->params->get('codebeforebody');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<?php		// Add custom code after opening head tag
			if($codeafterhead != null) : ?>
			<?php echo $codeafterhead;
		?>	
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		
		<?php		// Output as HTML5
			$this->setHtml5(true);
		?>
		<jdoc:include type="head" />
		
		<?php		// Use jQuery noConflict()
			if($noconflict == 0)  : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);
			?>
		<?php endif; ?>
		
		<?php		// Use jQuery Migrate
			if($jqmigrate >= 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
			?>
			<?php elseif($jqmigrate = 1) : ?>
				<script defer src="https://code.jquery.com/jquery-migrate-3.0.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
		<?php		// Use Joomla's jQuery 1.12.4 unless configured otherwise - Disabled for now
				//	if($jqlibrary > 0)  : ?>
				<?php //$doc = JFactory::getDocument();
				//		unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
			?>
		<?php // endif; ?>
		
		<?php		// Remove Joomla head scripts (third-party scripts will still load)
			if($killjoomlajs == 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/modals/js/script.min.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
					if (isset($this->_script['text/javascript'])) { 
						$this->_script['text/javascript'] = preg_replace('%window\.addEvent\    (\'load\',\s*function\(\)\s*{\s*new\s*JCaption\(\'img.caption\'\);\s*}\);\s*%',     '', $this->_script['text/javascript']);
						if (empty($this->_script['text/javascript']))
							unset($this->_script['text/javascript']);
						}
			?>
		<?php endif; ?>
		
		<?php 		// Remove Joomla CSS file (Modal styles)
			if($killjoomlacss == 1) : ?>
			<?php $doc = JFactory::getDocument();
				unset($doc->_scripts[JURI::root(true) . '/media/modals/css/bootstrap.min.css']); ?>
		<?php endif; ?>

		<?php 		// Remove Joomla generator tag
			if($killgenerator == 1) : ?>
			<?php $this->setGenerator(null); ?>
		<?php endif; ?>
   	 	
		<?php 		// Load remote Bootstrap 4.2.1 CSS framework from CDN
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
			<?php elseif(($bootstrapcdn == null) && ($bootstrapsource == 2)) : ?>
				<?php echo $bootstrapcdn ?>
			<?php else : ?>
		<?php endif; ?>
		
		<?php 		// Load the local CSS fixes for Joomla & Bootstrap 4.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs4.css" type="text/css" />
		<?php endif; ?>
		
		<?php 		// Load FontAwesome 5.6.3
			if($fontawesome == 1) : ?>
			<script defer src="https://use.fontawesome.com/releases/v5.6.3/js/all.js" integrity="sha384-EIHISlAOj4zgYieurP0SdoiBYfGJKkgWedPHH4jCzpCXLmzVsw1ouK59MuUtP4a1" crossorigin="anonymous"></script>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome.css" type="text/css" />
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome.css" type="text/css" />
		<?php endif; ?>
						
		<?php		/* Load Google Fonts  */		?>
		<?php if(($headerfont == 1) && ($bodyfont == 1) && ($headerfontname != null) && ($bodyfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $headerfontname; ?>|<?php echo $bodyfontname; ?>">
			<style>h1,h2,h3,h4,h5,h6{font-family:'<?php echo $headerfontname; ?>',serif;}body{font-family:'<?php echo $bodyfontname; ?>',serif;}</style>
		<?php elseif(($headerfont == 1) && ($bodyfont == 0) && ($headerfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $headerfontname; ?>">
			<style>h1,h2,h3,h4,h5,h6{font-family:'<?php echo $headerfontname; ?>',serif;}</style>
		<?php elseif(($headerfont == 0) && ($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $bodyfontname; ?>">
			<style>body{font-family:'<?php echo $bodyfontname; ?>',serif;}</style>
		<?php else : ?>
		<?php endif; ?>		
		
		<?php 		// Load the RTL CSS file.
			if($this->direction == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.css" type="text/css" />
		<?php endif; ?>
		
		<?php 		// Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
		<?php endif; ?>
		
		<?php 		// Add any custom user CSS from the configuration.
			if($customcsscode != null) : ?>
			<style>
				<?php echo $customcsscode ?>
			</style>
		<?php endif; ?>
		
		<?php 		// Load template favicons, loaded by default
			if($loadfavicons == 1) : ?>
				<link rel="apple-touch-icon" sizes="180x180" href="/templates/<?php echo $this->template ?>/favicons/apple-touch-icon.png">
				<link rel="icon" type="image/png" sizes="32x32" href="/templates/<?php echo $this->template ?>/favicons/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="16x16" href="/templates/<?php echo $this->template ?>/favicons/favicon-16x16.png">
				<link rel="manifest" href="/templates/<?php echo $this->template ?>/favicons/site.webmanifest">
				<link rel="mask-icon" href="/templates/<?php echo $this->template ?>/favicons/safari-pinned-tab.svg" color="#5bbad5">
				<link rel="shortcut icon" href="/templates/<?php echo $this->template ?>/favicons/favicon.ico">
				<meta name="msapplication-TileColor" content="#da532c">
				<meta name="msapplication-config" content="/templates/<?php echo $this->template ?>/favicons/browserconfig.xml">
				<meta name="theme-color" content="#ffffff">
		<?php endif; ?>
		
		<?php 		// Load custom local user JavaScript
			if($customjs == 1) : ?>
			<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<?php endif; ?>
		
		<?php		// Add custom code before closing head tag
			if($codebeforehead != null) : ?>
			<?php echo $codebeforehead;
		?>	
		<?php endif; ?>
	</head>
	
	<?php		/* Add the menu item alias to the body ID, class, or both  */		?>
	<?php if($bodymenu == 1) : ?>
		<body class="<?php echo $active->alias; ?> component-only ">
	<?php elseif($bodymenu == 2) : ?>
		<body id="<?php echo $active->alias; ?>" class="component-only">
	<?php elseif($bodymenu == 3) : ?>
		<body id="<?php echo $active->alias; ?>" class="<?php echo $active->alias; ?> component-only ">
	<?php else : ?>
		<body class="component-only">
	<?php endif; ?>
	
	<?php	// Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody;
	?>	
	<?php endif; ?>

	<?php 		// Choose either a fixed or fluid width container
		if($fluidcontainer == 1) : ?>
		<div class="container-fluid">
	<?php else : ?>
		<div class="container">
	<?php endif; ?>

			<div class="row">
				<div class="col-md-12">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
			</div>
		</div>
		
		<jdoc:include type="modules" name="debug" style="none" />
		
		<?php		/* Load jQuery 2 or 3 remotely.  */		?>
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<?php else : ?>
			<?php echo $jquerycdn ?>
		<?php endif; ?>		

		<?php		// Use jQuery Migrate 3.0.1
			if($jqmigrate == 1)  : ?>
				<script src="https://code.jquery.com/jquery-migrate-3.0.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
		<?php 		// Load the local or remote Bootstrap 3 or 4 JavaScript dependency
			 			// If CDN empty and load BS 3 remotely	
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
			
			<?php 		// If CDN empty and load BS 4 remotely
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3)) : ?>
			<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
			
			<?php 		// If CDN empty and load BS 4 remotely, but full jQuery 3 is loaded
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3) && ($jqlibrary == 2)) : ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
		<?php else : ?>
		<?php endif; ?>
				
		<?php 		// Add Google Analytics tag if configured.
			if($gacode != null) : ?>
		<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			  ga('create', '<?php echo $gacode; ?>', 'auto');
			  ga('send', 'pageview');
		</script>
		<?php endif; ?>
		
		<?php	// Add custom code before closing body tag
			if($codebeforebody != null) : ?>
			<?php echo $codebeforebody;
		?>	
		<?php endif; ?>
	</body>
</html>
