<?php
/**
 * @copyright	Copyright (C) 2022 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		<meta name="robots" content="noindex">
		<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
		<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/js/all.min.js"></script>
	</head>
	<body class="error">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<p class="text-center mt-5">
						<span style="font-size: 6em;" class="text-danger"><i class="fas fa-pizza-slice"></i></span>
					</p>
					<h1 class="text-center text-danger">Oops. Looks like you're lost.</h1>
					
					<p class="text-center">There are a number of reasons this might have happened, but more importantly let's get you to where you want to go.</p>
					
					<p class="text-center"><a href="<?php echo $this->baseurl; ?>/index.php" title="Home Page">Go to the home page <i class="fas fa-external-link-alt"></i></a></p>
					
					<p class="text-center"><a href="javascript: history.go(-1)" title="Back to the previous page">Go back to the previous page <i class="fas fa-external-link-alt"></i></a></p>
						
					<p class="text-center"><a href="<?php echo $this->baseurl; ?>/index.php?option=com_search" title="Search the website">Search the website <i class="fas fa-external-link-alt"></i></a></p>
						
					<p class="text-center">If problems continue, please contact the Website Administrator and report the error below.</p>
					
					<div class="tech-info">
					<p class="text-center text-muted">ERROR: <?php echo $this->error->getCode(); ?> - <?php echo $this->error->getMessage(); ?></p>
					<p class="text-center text-muted"><?php echo $this->error->getMessage(); ?></p>
					<p class="text-center text-muted">
						<?php if ($this->debug) :
							echo $this->renderBacktrace();
							endif; ?>
					</p>
				</div>
			</div>
		</div>
	</body>
</html>