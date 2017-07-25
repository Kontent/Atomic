<?php
/**
 * @copyright	Copyright (C) 2017 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/* The following line gets the application object for things like displaying the site name */
$app  = JFactory::getApplication();
$user = JFactory::getUser();

// Output as HTML5
$this->setHtml5(true);

// Getting params from template
$params = $app->getTemplate(true)->params;

// Get params
$sitetitle								= $this->params->get('sitetitle');
$sitedescription					= $this->params->get('sitedescription');
$bootstraplocal					= $this->params->get('bootstraplocal');
$bootstraplocaltheme			= $this->params->get('bootstraplocaltheme');
$bootstrapcdn						= $this->params->get('bootstrapcdn');
$bootswatch						= $this->params->get('bootswatch');
$fontawesome						= $this->params->get('fontawesome');
$elegantfont						= $this->params->get('elegantfont');
$customcss							= $this->params->get('customcss');
$customjs							= $this->params->get('customjs');
$fluidcontainer						= $this->params->get('fluidcontainer');
$jqlibrary								= $this->params->get('jqlibrary');
$jquerycdn							= $this->params->get('jquerycdn');
$bs3joomla							= $this->params->get('bs3joomla');
$gacode								= $this->params->get('gacode');
$pageheader						= $this->params->get('pageheader');
$topmenu							= $this->params->get('topmenu');
$abovebody							= $this->params->get('abovebody');
$leftbody								= $this->params->get('leftbody');
$rightbody							= $this->params->get('rightbody');
$belowbody							= $this->params->get('belowbody');
$footer									= $this->params->get('footer');
$headerfont							= $this->params->get('headerfont');
$headerfontname				= $this->params->get('headerfontname');
$bodyfont							= $this->params->get('bodyfont');
$bodyfontname					= $this->params->get('bodyfontname');
$killjoomlajs							= $this->params->get('killjoomlajs');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		
		<?php if($killjoomlajs == 1) : ?>
			<?php
				// Remove Joomla head scripts
				$doc = JFactory::getDocument();
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
			?>
		<?php endif; ?>
		
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- Load the Bootstrap 3 CSS Framework. -->
		<?php if(($bootstrapcdn == null) && ($bootstraplocal == 0)) : ?>	
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.min.css" type="text/css" />
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		 	 <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<?php elseif(($bootstrapcdn == null) && ($bootstraplocal == 1)) : ?>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<?php else : ?>
			<?php echo $bootstrapcdn ?>
		<?php endif; ?>
		
		<?php if($bootstraplocaltheme == 1) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap-theme.min.css" type="text/css" />
		<?php endif; ?>		
			
		<?php if($bootswatch == 'cerulean') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cerulean/bootstrap.min.css" rel="stylesheet" integrity="sha384-zF4BRsG/fLiTGfR9QL82DrilZxrwgY/+du4p/c7J72zZj+FLYq4zY00RylP9ZjiT" crossorigin="anonymous">
		<?php elseif($bootswatch == 'cosmo') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">
		<?php elseif($bootswatch == 'cyborg') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cyborg/bootstrap.min.css" rel="stylesheet" integrity="sha384-D9XILkoivXN+bcvB2kSOowkIvIcBbNdoDQvfBNsxYAIieZbx8/SI4NeUvrRGCpDi" crossorigin="anonymous">
		<?php elseif($bootswatch == 'darkly') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-S7YMK1xjUjSpEnF4P8hPUcgjXYLZKK3fQW1j5ObLSl787II9p8RO9XUGehRmKsxd" crossorigin="anonymous">
		<?php elseif($bootswatch == 'flatly') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css" rel="stylesheet" integrity="sha384-+ENW/yibaokMnme+vBLnHMphUYxHs34h9lpdbSLuAwGkOKFRl4C34WkjazBtb7eT" crossorigin="anonymous">
		<?php elseif($bootswatch == 'journal') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-1L94saFXWAvEw88RkpRz8r28eQMvt7kG9ux3DdCqya/P3CfLNtgqzMnyaUa49Pl2" crossorigin="anonymous">
		<?php elseif($bootswatch == 'lumen') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/lumen/bootstrap.min.css" rel="stylesheet" integrity="sha384-gv0oNvwnqzF6ULI9TVsSmnULNb3zasNysvWwfT/s4l8k5I+g6oFz9dye0wg3rQ2Q" crossorigin="anonymous">
		<?php elseif($bootswatch == 'paper') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/paper/bootstrap.min.css" rel="stylesheet" integrity="sha384-awusxf8AUojygHf2+joICySzB780jVvQaVCAt1clU3QsyAitLGul28Qxb2r1e5g+" crossorigin="anonymous">
		<?php elseif($bootswatch == 'readable') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css" rel="stylesheet" integrity="sha384-Li5uVfY2bSkD3WQyiHX8tJd0aMF91rMrQP5aAewFkHkVSTT2TmD2PehZeMmm7aiL" crossorigin="anonymous">
		<?php elseif($bootswatch == 'sandstone') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/sandstone/bootstrap.min.css" rel="stylesheet" integrity="sha384-G3G7OsJCbOk1USkOY4RfeX1z27YaWrZ1YuaQ5tbuawed9IoreRDpWpTkZLXQfPm3" crossorigin="anonymous">
		<?php elseif($bootswatch == 'simplex') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/simplex/bootstrap.min.css" rel="stylesheet" integrity="sha384-C0X5qw1DlkeV0RDunhmi4cUBUkPDTvUqzElcNWm1NI2T4k8tKMZ+wRPQOhZfSJ9N" crossorigin="anonymous">
		<?php elseif($bootswatch == 'slate') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-RpX8okQqCyUNG7PlOYNybyJXYTtGQH+7rIKiVvg1DLg6jahLEk47VvpUyS+E2/uJ" crossorigin="anonymous">
		<?php elseif($bootswatch == 'solar') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/solar/bootstrap.min.css" rel="stylesheet" integrity="sha384-GC77SCz5O11gVtXl0sSfbQYEWSSznn1wPDHgL1BGUTFU9iEoUrG4IOJa5CBVY8kR" crossorigin="anonymous">
		<?php elseif($bootswatch == 'spacelab') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/spacelab/bootstrap.min.css" rel="stylesheet" integrity="sha384-L/tgI3wSsbb3f/nW9V6Yqlaw3Gj7mpE56LWrhew/c8MIhAYWZ/FNirA64AVkB5pI" crossorigin="anonymous">
		<?php elseif($bootswatch == 'superhero') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/superhero/bootstrap.min.css" rel="stylesheet" integrity="sha384-Xqcy5ttufkC3rBa8EdiAyA1VgOGrmel2Y+wxm4K3kI3fcjTWlDWrlnxyD6hOi3PF" crossorigin="anonymous">
		<?php elseif($bootswatch == 'united') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/united/bootstrap.min.css" rel="stylesheet" integrity="sha384-pVJelSCJ58Og1XDc2E95RVYHZDPb9AVyXsI8NoVpB2xmtxoZKJePbMfE4mlXw7BJ" crossorigin="anonymous">
		<?php elseif($bootswatch == 'yeti') : ?>
			<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/yeti/bootstrap.min.css" rel="stylesheet" integrity="sha384-HzUaiJdCTIY/RL2vDPRGdEQHHahjzwoJJzGUkYjHVzTwXFQ2QN/nVgX7tzoMW3Ov" crossorigin="anonymous">
		<?php endif; ?>		
		
		<?php if($bs3joomla == 1) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs3.css" type="text/css" />
		<?php endif; ?>
		
		<?php if($fontawesome == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.min.css" type="text/css" />
		<?php elseif($fontawesome == 2) : ?>
			<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
		<?php endif; ?>
		
		<?php if($elegantfont == 1) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/elegant.css" type="text/css" />
		<?php endif; ?>
		
		<?php if($this->direction == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.css" type="text/css" />
		<?php endif; ?>
		
		<?php if($customcss == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
		<?php endif; ?>
				
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
		
		<?php if($customjs == 1) : ?>
			<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<?php endif; ?>
				
	</head>
	<body>
	
	<?php if($fluidcontainer == 1) : ?>
		<div class="container-fluid">
	<?php else : ?>
		<div class="container">
	<?php endif; ?>
	
		<?php if($pageheader == 1) : ?>
		<div class="row">
			<div class="page-header">
				<h1><?php if($sitetitle == null) : ?><?php echo htmlspecialchars($app->getCfg('sitename')); ?>
					<?php else : ?><?php echo $sitetitle	; ?><?php endif; ?></h1>
					<?php if($sitedescription != null) : ?><p><small><?php echo $sitedescription	; ?></small></p><?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if($topmenu == 1) : ?>
		<div class="row">
			<div class="col-md-9">
				<nav class="navigation" role="navigation">
					<div class="nav-collapse">
						<jdoc:include type="modules" name="navigation" style="none" />
					</div>
				</nav>
			</div>
 			<div class="col-md-3">
 				<jdoc:include type="modules" name="search" style="none" />
 			</div>
		</div>
		<?php endif; ?>
		
		<div class="row">

			<?php if(($leftbody == 1) && ($rightbody == 1)) : ?>	
			<div class="col-md-2">
 				<jdoc:include type="modules" name="leftbody" />
 			</div>
 			<div class="col-md-8">
 				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<?php if($belowbody == 1) : ?>
				<jdoc:include type="modules" name="belowbody" />
				<?php endif; ?>
 			</div>
			<div class="col-md-2">
 				<jdoc:include type="modules" name="rightbody" />
 			</div>
 			
			<?php elseif(($leftbody == 0) && ($rightbody == 1)) : ?>
 			<div class="col-md-10">
 				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<?php if($belowbody == 1) : ?>
				<jdoc:include type="modules" name="belowbody" />
				<?php endif; ?>
 			</div>
			<div class="col-md-2">
 				<jdoc:include type="modules" name="rightbody"  />
 			</div>
		
			<?php elseif(($leftbody == 1) && ($rightbody == 0)) : ?>
			<div class="col-md-2">
 				<jdoc:include type="modules" name="leftbody"  />
 			</div>
 			<div class="col-md-10">
 				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<?php if($belowbody == 1) : ?>
				<jdoc:include type="modules" name="belowbody" />
				<?php endif; ?>
 			</div>
 			
			<?php else : ?>
 			<div class="col-md-12">
 				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<?php if($belowbody == 1) : ?>
				<jdoc:include type="modules" name="belowbody" />
				<?php endif; ?>
 			</div>
			<?php endif; ?>
				 			
		</div>
		
		<?php if($footer == 1) : ?>
		<div class="row">
			<footer>
				<jdoc:include type="modules" name="footer" style="none" />
				<hr />
				&copy;<?php echo date('Y'); ?> <?php echo htmlspecialchars($app->getCfg('sitename')); ?>
			</footer>
		</div>
		<?php endif; ?>
	</div>
		
		<jdoc:include type="modules" name="debug" style="none" />
    				
		<?php if (($jquerycdn == null) && ($jqlibrary == 0) && ($bootstraplocal == 0)) : ?>	
			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/bootstrap.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 0) && ($bootstraplocal == 1)) : ?>
			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<?php endif; ?>
		
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<?php else : ?>
			<?php echo $jquerycdn ?>
		<?php endif; ?>		
		
		
		<?php if($gacode != null) : ?>
		<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', '<?php echo $gacode; ?>', 'auto');
			  ga('send', 'pageview');
		</script>
		<?php endif; ?>
	</body>
</html>
