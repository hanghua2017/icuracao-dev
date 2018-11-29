<!DOCTYPE html>
<html>

<head>
	
	<meta charset="utf-8">
	<!-- correct the ios safari auto correct for phone number -->
	<meta name="format-detection" content="telephone=no">
	<title>Curacao Credit Application</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>  
  <link rel="stylesheet" type="text/css" href="/ci_media/css/normalize.css" />
  <link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step0.css" />
	<link rel="shortcut icon" type="image/ico" href="/ci_media/images/favicon.ico">
  
  <script type="text/javascript" src="/ci_media/js/preapprove/main.js"></script>
    
  </script>
	
  <title>Curacao Credit App</title>
	
</head>
<body>
	
  <div id="loader" style="display:none;">
		<div>
			<img src="/ci_media/images/logo-animation.gif">
			<span id="loadermessage"></span>
		</div>
	</div>
	<div id="header" class="page-header landing">
		
		<div class="header-center">
			<img class="header-image" src="/ci_media/images/logo.png" alt="Curacao"/>		
			<div class="desktop-only">
				<div class="header-right">
					<span class="header-text">Apply by Phone</span><span class="header-phone"> 1 (866)410-1611</span><br/>
					<?php $lang = $this->session->userdata('site_lang');?>
					<?php if($lang != 'spanish'){ ?>
						<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
					<?php }else{ ?>
						<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="mobile-only">
		<?php $lang = $this->session->userdata('site_lang');?>
		<?php if($lang != 'spanish'){ ?>
			<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
		<?php }else{ ?>
			<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
		<?php } ?>
		</div>
		
	</div>
      
  <!-- <div>	
    Icon from assets folder:
    <img src="/ci_media/images/credit_app/favicon.ico"/>
  </div> -->

</body>
</html>