<?php
/**
 * @copyright	Copyright (C) 2008-2022 Ron Severdia. All rights reserved.
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
$scrollreveal						= $this->params->get('scrollreveal');
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
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
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
		
		<?php		//	 Use Scroll Reveal
			if($scrollreveal == 1) : ?>
			<script src="https://unpkg.com/scrollreveal"></script>
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
   	 	   	 	
		<?php		//	 Load local or remote Bootstrap 4.6 CSS framework, or from CDN
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
			<?php elseif(($bootstrapcdn == null) && ($bootstrapsource == 2)) : ?>
				<?php echo $bootstrapcdn ?>
			<?php else : ?>
		<?php endif; ?>
		
		<?php 		//	 Load Bootswatch theme. Themes also include BS.
				
			if($bootswatch == 1) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/cerulean/bootstrap.min.css" integrity="sha512-u5+wLyKje1GFWkxtj9SBywNE4jmbgXUVJU24sDC4LShQksachiXuDS6syD75F414AiATXhV7eTqFO7kegoRu1g==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 2) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/cosmo/bootstrap.min.css" integrity="sha512-PdJ/GdNpssn/PJ867hkDvYX2VHLsJoLtu1glpNDxCH7VacSGJ2FIWrOXWzBN77qV4kGdwOwXZmmznWjAsGHa0g==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 3) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/flatly/bootstrap.min.css" integrity="sha512-5bGVgbI2xuyCes5Q7colxgLChuX/2lidwyC6zFo0Fu7Nb46xf55YcMwojQel2JBxaJoa3w0d14dKek6TbGROfQ==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 4) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/journal/bootstrap.min.css" integrity="sha512-F+JIviHiHFl2uTOrUOe3ssKWmqX725jGHse17rqA+B48PZktPE69m1ZT8r/7omHKOtScvQh6QIwxc6cZ8JacKg==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 5) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/litera/bootstrap.min.css" integrity="sha512-wX6nlCNl4dU3nJ7LyvHnD/I3tA51vhx1QVYQYOtCHuaSQGnuSaS0Y1V0Sv2AeLjQnjbiUCn4ci6CaXdDBMEplw==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 6) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/lumen/bootstrap.min.css" integrity="sha512-k7f744z7yElaeTu/FuqJGQ9G3YJ2VVN7r00mkreJCg7t0mera+rLuCd+uBuarIdTNfrI8xq/fFE9mOIbG9jeXA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 7) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/lux/bootstrap.min.css" integrity="sha512-WuuAGQTSGG3228iS6X391fHFzL6x65yWGGLilC1XYcXY6gT/6EHofWqsjLBsunqLtq7pDzcYOjW21Nn9y9Q75g==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 8) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/materia/bootstrap.min.css" integrity="sha512-7exGAnRoaHLuhukDlCpSYr0i9q0oXSzkC35uZSF2bJmnyZCrsWs3h+YZKKsLe9Tb5TfYduhNbBwvYef9XN64gA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 9) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/minty/bootstrap.min.css" integrity="sha512-cJfSWSAaQhijZtTYlN999y0ZPyzstO5T7M9ok7gBi8ad9vu9ibPDBu8gEbu/GLlRR3Tt2RAM2kTxX2H+EMicEA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 10) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/pulse/bootstrap.min.css" integrity="sha512-hsigz5HlhRSxo/G/HVgixFsjgnsPRlIcHceeAFfStolvxPxtcSNNEia0RNs8F9KqxUV2CYivxiK/53Q8Mwlm1A==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 11) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/sandstone/bootstrap.min.css" integrity="sha512-XsxfGIdvnQW/g1t0gyABAyAw/xP51TmpK5iXG6vfNEU+5qE+nl/d+NkAKHCcvDSEmgSUJbN65SR9/eZEUkfXIA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 12) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/simplex/bootstrap.min.css" integrity="sha512-9hj+qhrmo7MUSzKG3nwkDWncL1x8e2d1wfJxufofoBMMLXlqlqvjpT0V0blusJ8CFx9fs9Ru7ICYkVrz62Q33w==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 13) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/sketchy/bootstrap.min.css" integrity="sha512-AF3OQszBqW7CeiKglwNTnTwPCbJvAo6DVXLbU37Qcw7nPxVPywkPbDCZyGpLu0xqzPeQL2GKAQ5k69hMBTI7LA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 14) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/spacelab/bootstrap.min.css" integrity="sha512-1gJsJyef+f5RKK96Vq34R3Lc9KJr21Ss2PcXosqp+1Z1yWpCKXL35p3E5nYxj4PPSfjFUc6elocMw9AiXrGXAQ==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 15) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/united/bootstrap.min.css" integrity="sha512-D/XTI3HHxegvO0hcG75yd++4QyP+AM5IqkxFRu1KBhBQYwxYdWi+JP9DlBppR3GiMBXlAZWuw2qmHQWobofXQA==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 16) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/yeti/bootstrap.min.css" integrity="sha512-GVYbMbb9dzILPv8eNN22wE2YJtMNdPdgPCPs8Aoz9FkCRTvt2c60Tg1QYnxucm6ayz10YPiQ/btuu4UuMYdN9g==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 17) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/cyborg/bootstrap.min.css" integrity="sha512-btcJYpZJYw8Q01/asshezM2QraXI5OMl47spBQ6yHnAHhevR2EOnWDP0VJznX8fpvtuWu289XafQpcO6I4e3+A==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 18) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css" integrity="sha512-A/WvCV75maYUI3F3yjeSqYg0dUIepPRx14Qw8EZjJ/udG5/s3uDWLHnm1FSbYzrJg4RLdAdEm/f6+1V6AxCBJQ==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 19) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/slate/bootstrap.min.css" integrity="sha512-o5yeKjohzmmY4mschw0nYnJxQJu+2xrDLeWhTG57dFRuXyzWX2630AwHExfxuFxfaBYUuRQzP7ggJJYhfRnVqw==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 20) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/solar/bootstrap.min.css" integrity="sha512-4erGAXsEsXF4aZeb9ye7RJadu03zIuEW0CzuEKgsCa3xrBj/smW1TDZ07ZiK7o5Yk96IB4iQgQ0LhqjFeHqtLg==" crossorigin="anonymous" />
				<?php elseif($bootswatch == 21) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/superhero/bootstrap.min.css" integrity="sha512-Slx3+m+CsW6eBV9/9EaeSkHUQfieDfx4DmpTxtJIQwE6cwbEUgFsgEXohRIxNej8rWySQwm231R+FVg89hF4Qg==" crossorigin="anonymous" />
		<?php endif; ?>
		
		<?php		//	 Load the local CSS fixes for Joomla & Bootstrap 4.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs4.css" type="text/css">
		<?php endif; ?>
		
		<?php 		//	 Load FontAwesome 5.14
			if($fontawesome == 1) : ?>
			<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js"></script>
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
		<?php elseif($fontawesome == 3) : ?>
			<link rel="stylesheet" href="<?php echo $fontawesomecdn; ?>">
		<?php elseif($fontawesome == 4) : ?>
			<script defer src="<?php echo $fontawesomecdn; ?>"></script>
		<?php endif; ?>
		
		<?php		//	  Load Google Fonts 	?>
		<?php if(($headerfont == 1) && ($bodyfont == 1) && ($headerfontname != null) && ($bodyfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $headerfontname; ?>|<?php echo $bodyfontname; ?>&display=swap">
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
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
			
			<?php 		//	 If CDN empty and load BS 4 remotely, but full jQuery 3 is loaded
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3) && ($jqlibrary == 2)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
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
