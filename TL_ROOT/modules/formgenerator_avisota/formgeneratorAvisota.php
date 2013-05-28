<?php

class formgeneratorAvisota extends Frontend
{
  	public function sendDoubleOptInMail($arrPost, $arrForm, $arrFiles)
  	{
  		// Default is using the request_token from the form.
  		// But also, you can generate an own token by substr(md5(rand(0,1000).time()),0,15);
  		$token = $arrPost['REQUEST_TOKEN'];

  		if ($arrPost['newsletter_signup'] && $arrPost['email'])
  		{
  			// Check, if recipient already exists
  			$sql = 'SELECT id 
					FROM tl_avisota_recipient 
					WHERE email="'.$arrPost['email'].'"';

			$rs = mysql_query($sql);
			$data = mysql_fetch_assoc($rs);

			if(!$data)
			{
	  			// Send double-opt-in-mail
	  			$link = 'http://'.$_SERVER['HTTP_HOST'].'/a/'.$token;
				
				$objPlain = new FrontendTemplate('mail_subscribe_plain');
				$objPlain->link = $link;

				$objHtml = new FrontendTemplate('mail_subscribe_html');
				$objHtml->title = $GLOBALS['TL_LANG']['formgenerator_avisota']['subject'];
				$objHtml->link = $link;

				$objEmail = new Email();
				$objEmail->subject = $GLOBALS['TL_LANG']['formgenerator_avisota']['subject'];
				$objEmail->logFile = 'subscription.log';
				$objEmail->text = $objPlain->parse();
				$objEmail->html = $objHtml->parse();
				$objEmail->from = $objRoot->adminEmail;
				$objEmail->sendTo($arrPost['email']);

				// Save receipient to datebase
				if (is_array($arrPost['newsletter_signup']))
				{
					foreach ($arrPost['newsletter_signup'] as $pid) 
					{
						$this->Database->prepare('INSERT INTO tl_avisota_recipient 
							(
								pid, 
								tstamp, 
								email, 
								firstname, 
								lastname, 
								token, 
								addedOn
							) 
							VALUES 
							(
								'.$pid.',
								'.time().',
								"'.$arrPost['email'].'",
								"'.$arrPost['vorname'].'",
								"'.$arrPost['name'].'",
								"'.$token.'",
								'.time().'
							)')->execute();
					}
				}
				else
				{
					$this->Database->prepare('INSERT INTO tl_avisota_recipient 
						(
							pid, 
							tstamp, 
							email, 
							firstname, 
							lastname, 
							token, 
							addedOn
						) 
						VALUES 
						(
							'.$arrPost['newsletter_signup'].',
							'.time().',
							"'.$arrPost['email'].'",
							"'.$arrPost['vorname'].'",
							"'.$arrPost['name'].'",
							"'.$token.'",
							'.time().'
						)')->execute();
				}

	  		}
	  	}
  	}          
}

?>