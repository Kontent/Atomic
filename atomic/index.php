<?php
/**
 * @copyright	Copyright (C) 2020 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//	Get the application object for things like displaying the site name 
$app  = JFactory::getApplication();
$user = JFactory::getUser();

//	Output as HTML5
$this->setHtml5(true);

//	Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

//	Get params from template
$params = $app->getTemplate(true)->params;

//	Assign template params
$logo									= $this->params->get('logo');
$sitetitle								= $this->params->get('sitetitle');
$sitedescription					= $this->params->get('sitedescription');
$bodymenu							= $this->params->get('bodymenu');
$bootstrapsource				= $this->params->get('bootstrapsource');
$bootstrapcdn						= $this->params->get('bootstrapcdn');
$fontawesome						= $this->params->get('fontawesome');
$fontawesomecdn				= $this->params->get('fontawesomecdn');
$customcsscode					= $this->params->get('customcsscode');
$customcssfile					= $this->params->get('customcssfile');
$customjs							= $this->params->get('customjs');
$fluidcontainer						= $this->params->get('fluidcontainer');
$jqlibrary								= $this->params->get('jqlibrary');
$jquerycdn							= $this->params->get('jquerycdn');
$bsfixjoomla						= $this->params->get('bsfixjoomla');
$gacode								= $this->params->get('gacode');
$pageheader						= $this->params->get('pageheader');
$pageheadermod				= $this->params->get('pageheadermod');
$topmenu							= $this->params->get('topmenu');
$abovebody							= $this->params->get('abovebody');
$leftbody								= $this->params->get('leftbody');
$rightbody							= $this->params->get('rightbody');
$belowbody							= $this->params->get('belowbody');
$footer									= $this->params->get('footer');
$alertbar								= $this->params->get('alertbar');
$headerfont							= $this->params->get('headerfont');
$headerfontname				= $this->params->get('headerfontname');
$bodyfont							= $this->params->get('bodyfont');
$bodyfontname					= $this->params->get('bodyfontname');
$killjoomlajs							= $this->params->get('killjoomlajs');
$killjoomlacss						= $this->params->get('killjoomlacss');
$killgenerator						= $this->params->get('killgenerator');
$copyright							= $this->params->get('copyright');
$copyrighttxt						= $this->params->get('copyrighttxt');
$noconflict							= $this->params->get('noconflict');
$jqmigrate							= $this->params->get('jqmigrate');
$loadfavicons						= $this->params->get('loadfavicons');
$loadappleicons					= $this->params->get('loadappleicons');
$codeafterhead					= $this->params->get('codeafterhead');
$codebeforehead				= $this->params->get('codebeforehead');
$codeafterbody					= $this->params->get('codeafterbody');
$codebeforebody				= $this->params->get('codebeforebody');
$cssoverride						= $this->params->get('cssoverride');
$instant								= $this->params->get('instant');
$protopositions					= $this->params->get('protopositions');
$bootswatch						= $this->params->get('bootswatch');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<?php		//	 Add custom code after opening head tag
			if($codeafterhead != null) : ?>
			<?php echo $codeafterhead;
		?>	
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		
		<?php		//	 Output as HTML5
			$this->setHtml5(true);
		?>
		
		<?php		//	  Load jQuery 2 or 3 remotely	?>
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 3)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
		<?php else : ?>
			<script src="https://<?php echo $jquerycdn ?>"></script>
		<?php endif; ?>		
		
		<jdoc:include type="head" />
		
		<?php		//	 Remove Joomla head scripts (third-party scripts will still load)
			if($killjoomlajs == 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/modals/js/script.min.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
					if (isset($this->_script['text/javascript'])) { 
						$this->_script['text/javascript'] = preg_replace('/jQuery\(window\).on\(\'load\'\,  function\(\) \{(.*);/is', '', $this->_script['text/javascript']);
						if (empty($this->_script['text/javascript'])) {
							unset($this->_script['text/javascript']);
						}
					}
			?>
		<?php endif; ?>
		
		<?php		//	 Use jQuery noConflict()
			if($noconflict == 0)  : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);
			?>
		<?php endif; ?>
		
		<?php		//	 Use jQuery Migrate
			if($jqmigrate >= 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
			?>
			<?php elseif($jqmigrate = 1) : ?>
				<script defer src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
		<?php 		//	 Remove Joomla CSS file (Modal styles)
			if($killjoomlacss == 1) : ?>
			<?php $doc = JFactory::getDocument();
				unset($this->_stylesheets[JURI::root(true) . '/media/modals/css/bootstrap.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap-responsive.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap-extended.css']); 
				?>
		<?php endif; ?>

		<?php 		//	 Remove Joomla generator tag
			if($killgenerator == 1) : ?>
			<?php $this->setGenerator(null); ?>
		<?php endif; ?>
   	 	   	 	
		<?php		//	 Load local or remote Bootstrap 4.5 CSS framework, or from CDN
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
			<?php elseif(($bootstrapcdn == null) && ($bootstrapsource == 2)) : ?>
				<?php echo $bootstrapcdn ?>
			<?php else : ?>
		<?php endif; ?>
		
		<?php 		//	 Load Bootswatch theme. Themes also include BS.
			if($bootswatch == 1) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/cerulean/bootstrap.min.css" integrity="sha384-3fdgwJw17Bi87e1QQ4fsLn4rUFqWw//KU0g8TvV6quvahISRewev6/EocKNuJmEw" crossorigin="anonymous">
				<?php elseif($bootswatch == 2) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/cosmo/bootstrap.min.css" integrity="sha384-5QFXyVb+lrCzdN228VS3HmzpiE7ZVwLQtkt+0d9W43LQMzz4HBnnqvVxKg6O+04d" crossorigin="anonymous">
				<?php elseif($bootswatch == 3) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/flatly/bootstrap.min.css" integrity="sha384-qF/QmIAj5ZaYFAeQcrQ6bfVMAh4zZlrGwTPY7T/M+iTTLJqJBJjwwnsE5Y0mV7QK" crossorigin="anonymous">
				<?php elseif($bootswatch == 4) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/journal/bootstrap.min.css" integrity="sha384-QDSPDoVOoSWz2ypaRUidLmLYl4RyoBWI44iA5agn6jHegBxZkNqgm2eHb6yZ5bYs" crossorigin="anonymous">
				<?php elseif($bootswatch == 5) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/litera/bootstrap.min.css" integrity="sha384-enpDwFISL6M3ZGZ50Tjo8m65q06uLVnyvkFO3rsoW0UC15ATBFz3QEhr3hmxpYsn" crossorigin="anonymous">
				<?php elseif($bootswatch == 6) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lumen/bootstrap.min.css" integrity="sha384-GzaBcW6yPIfhF+6VpKMjxbTx6tvR/yRd/yJub90CqoIn2Tz4rRXlSpTFYMKHCifX" crossorigin="anonymous">
				<?php elseif($bootswatch == 7) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.css" integrity="sha384-9+PGKSqjRdkeAU7Eu4nkJU8RFaH8ace8HGXnkiKMP9I9Te0GJ4/km3L1Z8tXigpG" crossorigin="anonymous">
				<?php elseif($bootswatch == 8) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/materia/bootstrap.min.css" integrity="sha384-B4morbeopVCSpzeC1c4nyV0d0cqvlSAfyXVfrPJa25im5p+yEN/YmhlgQP/OyMZD" crossorigin="anonymous">
				<?php elseif($bootswatch == 9) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/minty/bootstrap.min.css" integrity="sha384-H4X+4tKc7b8s4GoMrylmy2ssQYpDHoqzPa9aKXbDwPoPUA3Ra8PA5dGzijN+ePnH" crossorigin="anonymous">
				<?php elseif($bootswatch == 10) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/pulse/bootstrap.min.css" integrity="sha384-L7+YG8QLqGvxQGffJ6utDKFwmGwtLcCjtwvonVZR/Ba2VzhpMwBz51GaXnUsuYbj" crossorigin="anonymous">
				<?php elseif($bootswatch == 11) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/sandstone/bootstrap.min.css" integrity="sha384-zEpdAL7W11eTKeoBJK1g79kgl9qjP7g84KfK3AZsuonx38n8ad+f5ZgXtoSDxPOh" crossorigin="anonymous">
				<?php elseif($bootswatch == 12) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/simplex/bootstrap.min.css" integrity="sha384-FYrl2Nk72fpV6+l3Bymt1zZhnQFK75ipDqPXK0sOR0f/zeOSZ45/tKlsKucQyjSp" crossorigin="anonymous">
				<?php elseif($bootswatch == 13) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/sketchy/bootstrap.min.css" integrity="sha384-RxqHG2ilm4r6aFRpGmBbGTjsqwfqHOKy1ArsMhHusnRO47jcGqpIQqlQK/kmGy9R" crossorigin="anonymous">
				<?php elseif($bootswatch == 14) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/spacelab/bootstrap.min.css" integrity="sha384-F1AY0h4TrtJ8OCUQYOzhcFzUTxSOxuaaJ4BeagvyQL8N9mE4hrXjdDsNx249NpEc" crossorigin="anonymous">
				<?php elseif($bootswatch == 15) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/united/bootstrap.min.css" integrity="sha384-JW3PJkbqVWtBhuV/gsuyVVt3m/ecRJjwXC3gCXlTzZZV+zIEEl6AnryAriT7GWYm" crossorigin="anonymous">
				<?php elseif($bootswatch == 16) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/yeti/bootstrap.min.css" integrity="sha384-mLBxp+1RMvmQmXOjBzRjqqr0dP9VHU2tb3FK6VB0fJN/AOu7/y+CAeYeWJZ4b3ii" crossorigin="anonymous">
				<?php elseif($bootswatch == 17) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/cyborg/bootstrap.min.css" integrity="sha384-nEnU7Ae+3lD52AK+RGNzgieBWMnEfgTbRHIwEvp1XXPdqdO6uLTd/NwXbzboqjc2" crossorigin="anonymous">
				<?php elseif($bootswatch == 18) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/darkly/bootstrap.min.css" integrity="sha384-nNK9n28pDUDDgIiIqZ/MiyO3F4/9vsMtReZK39klb/MtkZI3/LtjSjlmyVPS3KdN" crossorigin="anonymous">
				<?php elseif($bootswatch == 19) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/slate/bootstrap.min.css" integrity="sha384-8iuq0iaMHpnH2vSyvZMSIqQuUnQA7QM+f6srIdlgBrTSEyd//AWNMyEaSF2yPzNQ" crossorigin="anonymous">
				<?php elseif($bootswatch == 20) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/solar/bootstrap.min.css" integrity="sha384-NCwXci5f5ZqlDw+m7FwZSAwboa0svoPPylIW3Nf+GBDsyVum+yArYnaFLE9UDzLd" crossorigin="anonymous">
				<?php elseif($bootswatch == 21) : ?>
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/superhero/bootstrap.min.css" integrity="sha384-HnTY+mLT0stQlOwD3wcAzSVAZbrBp141qwfR4WfTqVQKSgmcgzk+oP0ieIyrxiFO" crossorigin="anonymous">
		<?php endif; ?>
		
		<?php		//	 Load the local CSS fixes for Joomla & Bootstrap 4.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs4.css" type="text/css">
		<?php endif; ?>
		
		<?php 		//	 Load FontAwesome 5.14
			if($fontawesome == 1) : ?>
			<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
		<?php elseif($fontawesome == 3) : ?>
			<link rel="stylesheet" href="<?php echo $fontawesomecdn; ?>">
		<?php elseif($fontawesome == 4) : ?>
			<script defer src="<?php echo $fontawesomecdn; ?>"></script>
		<?php endif; ?>
		
		<?php		//	  Load Google Fonts 	?>
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
		
		<?php 		//	 Load the RTL CSS file.
			if($this->direction == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Load the local CSS file for custom user CSS. 
			if($cssoverride == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Add any custom user CSS from the configuration.
			if($customcsscode != null) : ?>
			<style>
				<?php echo $customcsscode ?>
			</style>
		<?php endif; ?>
		
		<?php		//	 Load template favicons, loaded by default
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
		<?php		//	 Load Apple/Android precomposed icons
			if($loadappleicons == 1) : ?>
				<link rel="icon" sizes="192x192" href="touch-icon-192x192.png" ><!-- Android -->
				<link rel="apple-touch-icon-precomposed" sizes="180x180" href="<?php echo $this->baseurl ?>apple-touch-icon-180x180-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo $this->baseurl ?>apple-touch-icon-152x152-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $this->baseurl ?>apple-touch-icon-144x144-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo $this->baseurl ?>apple-touch-icon-120x120-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $this->baseurl ?>apple-touch-icon-114x114-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo $this->baseurl ?>apple-touch-icon-76x76-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $this->baseurl ?>apple-touch-icon-72x72-precomposed.png">
				<link rel="apple-touch-icon-precomposed" href="<?php echo $this->baseurl ?>apple-touch-icon-precomposed.png"><!-- 57×57px -->
		<?php endif; ?>
		
		<?php		//	 Load custom local user JavaScript
			if($customjs == 1) : ?>
			<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<?php endif; ?>
				
		<?php 		//	 Add Google Analytics tag if configured.
		if($gacode != null) : ?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gacode; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<?php echo $gacode; ?>');
		</script>
		<?php endif; ?>
		
		<?php		//	 Add custom code before closing head tag
			if($codebeforehead != null) : ?>
			<?php echo $codebeforehead;
		?>	
		<?php endif; ?>
		
	</head>
	
	<?php		//	 Add the menu item alias to the body ID, class, or both. Add protostar fixes. 	?>
	<?php if($bodymenu == 1) : ?>	
		<body class="<?php echo $active->alias; ?> <?php if($protopositions == 1) : ?>protostar<?php endif; ?> ">
	<?php elseif($bodymenu == 2) : ?>
		<body id="<?php echo $active->alias; ?>">
	<?php elseif($bodymenu == 3) : ?>
		<body id="<?php echo $active->alias; ?>" class="<?php echo $active->alias; ?> <?php if($protopositions == 1) : ?>protostar<?php endif; ?> ">
	<?php elseif($protopositions == 1) : ?>
		<body class="protostar">
	<?php else : ?>
		<body>
	<?php endif; ?>
	
	<?php		//	 Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody;
	?>	
	<?php endif; ?>

	<?php 		//	 Choose either a fixed or fluid width container
		if($fluidcontainer == 1) : ?>
		<div class="container-fluid">
	<?php else : ?>
		<div class="container">
	<?php endif; ?>
	
	<?php		//	 Load the mobile menu position if a module published	
		if ($this->countModules( 'mobilemenu' )) : ?>
		<jdoc:include type="modules" name="mobilemenu" style="mobilemenu" />
	<?php endif; ?>

		<?php if(($pageheader == 1) || ($pageheadermod == 1)) : ?>	
			<div class="row">
				<div class="page-header">
				<?php if($pageheader == 1) : ?>
					<h1>
						<?php if ($logo) : ?>
							<span id="logo">
								<a href="<?php echo $this->baseurl; ?>"><img src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($this->params->get('sitetitle')); ?>" /></a>
							</span>
						<?php endif;?>
						<?php if($sitetitle != null) : ?><?php echo $sitetitle	; ?><?php endif; ?></h1>
						<?php if($sitedescription != null) : ?><p><small><?php echo $sitedescription; ?></small></p><?php endif; ?>
				<?php endif; ?>
				<?php if($pageheadermod == 1) : ?>
					<jdoc:include type="modules" name="pageheader" style="basic" />
				<?php endif; ?>
				</div>
				<?php if ($this->countModules('menu')) : ?>
				<div class="headermenu"><jdoc:include type="modules" name="menu"style="basic" /></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		
			<?php if($topmenu == 1) : ?>
			<div class="row">
				<div class="col-md-9">
					<nav class="navigation">
						<div class="nav-collapse">
							<jdoc:include type="modules" name="navigation" style="basic" />
							<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-1" style="none" /><?php endif; ?>
						</div>
					</nav>
				</div>
				<div class="col-md-3">
					<jdoc:include type="modules" name="search" style="basic" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-0" style="none" /><?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<div class="row">
				<?php if(($leftbody == 1) && ($rightbody == 1)) : ?>	
				<div class="col-md-2 leftbody">
					<jdoc:include type="modules" name="leftbody"  style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-8" style="xhtml" /><?php endif; ?>
				</div>
				<div class="col-12 col-md-7 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<div class="col-md-3 rightbody">
					<jdoc:include type="modules" name="rightbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-7" style="default" /><?php endif; ?>
				</div>
			
				<?php elseif(($leftbody == 0) && ($rightbody == 1)) : ?>
				<div class="col-md-9 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody"style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<div class="col-md-3 rightbody">
					<jdoc:include type="modules" name="rightbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-7" style="default" /><?php endif; ?>
				</div>
		
				<?php elseif(($leftbody == 1) && ($rightbody == 0)) : ?>
				<div class="col-md-3 leftbody">
					<jdoc:include type="modules" name="leftbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-8" style="xhtml" /><?php endif; ?>
				</div>
				<div class="col-md-9 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
			
				<?php else : ?>
				<div class="col-md-12 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		
			<?php if($footer == 1) : ?>
			<footer class="container">
				<div class="row">
					<jdoc:include type="modules" name="footer" style="none"style="basic" />
					<hr />
					<small>
						<?php if(($copyrighttxt != null) && ($copyright == 1)) : ?>
						&copy;<?php echo date('Y'); ?> <?php echo $copyrighttxt ?>
						<?php else : ?>
						&copy;<?php echo date('Y'); ?> <?php echo htmlspecialchars($app->getCfg('sitename')); ?>
						<?php endif; ?>
					</small>
				</div>
			</footer>
			<?php endif; ?>
			
			<?php if ($this->countModules( 'alertbar' )) : ?>
				<?php if($alertbar == 1) : ?>
				<div id="alertbar"><jdoc:include type="modules" name="alertbar" style="none" /></div>
				<?php endif; ?>
			<?php endif; ?>
		
			<jdoc:include type="modules" name="debug" style="basic" />
		</div>

		<?php		//	 Use jQuery Migrate 3.3.1
			if($jqmigrate == 1)  : ?>
				<script src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
			<?php 		//	 Load the local or remote Bootstrap 3 or 4 JavaScript dependency
			 				//	 If CDN empty and load BS 3 remotely	
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
			
			<?php 		//	 If CDN empty and load BS 4 remotely
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3)) : ?>
			<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
			
			<?php 		//	 If CDN empty and load BS 4 remotely, but full jQuery 3 is loaded
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3) && ($jqlibrary == 2)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
		<?php else : ?>
		<?php endif; ?>	
		
		<?php		//	 Add custom code before closing body tag
			if($codebeforebody != null) : ?>
			<?php echo $codebeforebody; ?>	
		<?php endif; ?>
		
		<?php		//	 Use Instant.page
			if($instant == 1) : ?>
			<script src="//instant.page/5.1.0" type="module" integrity="sha384-by67kQnR+pyfy8yWP4kPO12fHKRLHZPfEsiSXR8u2IKcTdxD805MGUXBzVPnkLHw"></script>
		<?php endif; ?>
		
	</body>
</html>
