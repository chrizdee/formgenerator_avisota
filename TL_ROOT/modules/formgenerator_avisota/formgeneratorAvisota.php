<?php

class formgeneratorAvisota extends Frontend
{
  	public function replaceInsertTags($strTag)
    {
        $arrSplit = explode('::', $strTag);

        if ($arrSplit[0] == 'fga')
        {
            if(isset($arrSplit[1]) && $arrSplit[1] == 'message')
	    	{
		    	session_start();
				return $_SESSION['double_opt_in_message'];
	    	}
        }

        return false;
      }


  	public function sendDoubleOptInMail($arrPost, $arrForm, $arrFiles)
  	{
  		// Default is using the request_token from the form.
  		// But also, you can generate an own token by substr(md5(rand(0,1000).time()),0,15);
  		$token = $arrPost['REQUEST_TOKEN'];

  		if ($arrPost['newsletter_signup'] && $arrPost['email'])
  		{
  			// Check, if recipient already exists
  			foreach ($arrPost['newsletter_signup'] as $pid) 
			{
	  			$sql = 'SELECT id 
						FROM tl_avisota_recipient 
						WHERE email="'.$arrPost['email'].'" AND pid='.$pid;

				$rs = mysql_query($sql);
				$data = mysql_fetch_assoc($rs);

				if ($data)
				{
					// Get recipient list title
					$sql = 'SELECT title 
							FROM tl_avisota_recipient_list 
							WHERE id='.$pid;

					$rs = mysql_query($sql);
					$data = mysql_fetch_assoc($rs);
					
					$already_in_list .= $data['title'].', ';
					$list[$pid] = true;
				}
				else
				{
					$send_double_opt_in = true;
				}

			}

			session_start();

			if ($already_in_list)
				$_SESSION['double_opt_in_message'] = '<p class="message notice">'.$GLOBALS['TL_LANG']['formgenerator_avisota']['message'].substr($already_in_list, 0, -2).'</p>';
			else
				unset($_SESSION['double_opt_in_message']);

			if ($send_double_opt_in)
			{
	  			// Send double-opt-in-mail
	  			$link = 'http://'.$_SERVER['HTTP_HOST'].'/a/'.$token;
				
				$objPlain = new FrontendTemplate('mail_fga_subscribe_plain');
				$objPlain->link = $link;

				$objHtml = new FrontendTemplate('mail_fga_subscribe_html');
				$objHtml->title = $GLOBALS['TL_LANG']['formgenerator_avisota']['subject'];
				$objHtml->link = $link;

				$objEmail = new Email();
				$objEmail->subject = $GLOBALS['TL_LANG']['formgenerator_avisota']['subject'];
				$objEmail->logFile = 'subscription.log';
				$objEmail->text = $objPlain->parse();
				$objEmail->html = $objHtml->parse();
				$objEmail->from = $objRoot->adminEmail;
				$objEmail->sendTo($arrPost['email']);

				foreach ($arrPost['newsletter_signup'] as $pid) 
				{
					if (!$list[$pid])
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
							)'
						)->execute();
					}
				}
				

	  		}
	  	}
  	}          
}

?>