<?php

include_once(_BASEPATH ."classes/SMTPMailer.php");

function send_mail($mail_to, $mail_subject, $mail_body, $cc = '', $bcc = '', $attachment = '', $from_email = '', $from_name = '', $usePHPMailer = true)
{
	
	if (_SENDMAIL_ == true && $_SERVER['HTTP_HOST'] != 'localhost') {
		
		if ($from_email == '') {
			$from_email = _MAIL_FROM_EMAIL_;
		}
		if ($from_name == '') {
			$from_name = _MAIL_FROM_NAME_;
		}

        if ($usePHPMailer == false && $attachment == '') {

            $headers = "MIME-Version: 1.0" . "\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
            $headers .= "From: $from_name <$from_email>  \n";
            if ($cc) {
                $headers .= "Cc: $cc \n";
            }
            if ($bcc) {
                $headers .= "Bcc: $bcc \n";
            }

            mail($mail_to, $mail_subject, $mail_body, $headers, '-f' . $from_email);

            return 1;
        } else {
		
			try {
    // $mailer = new SMTPMailer("smtp.rediffmail.com", 586,995, "rkgiit94@rediffmail.com", "Way1@10#1", "ssl");
   // $mailer = new SMTPMailer("smtp.rediffmail.com", 586,995, "ramalayasales@prabhushriram.com", "ramalaya100", "ssl");
    $mailer = new SMTPMailer("smtp.gmail.com", 587, "ramalayasales@gmail.com", "xwvxqzvkupmpqhcm", "tls");
			 //$mailer = new SMTPMailer("smtp.hostinger.com", 586, "info@awesomesoft.in", "Rishabh@10#1", "tls");
			// $mailer = new SMTPMailer("smtp.rediffmailpro.com", 586, "ramalayasales@prabhushriram.com", "ramalaya100", "ssl");
			
			 // Set email properties
			$mailer->from = $from_email;
			$mailer->to = $mail_to;
			
			 if ($cc) {
                $mailer->cc = ["cc@example.com"]; // CC recipient(s)
            }
            if ($bcc) {
                $mailer->bcc = ["bcc@example.com"]; // BCC recipient(s)
            }
            
            
		
			$mailer->subject = $mail_subject;
			$mailer->body = $mail_body;

			// Add attachments
			$attachment_arr = [];
			$attachment_list = explode(',', $attachment);
            for ($i = 0; $i < count($attachment_list); $i++) {
                $email_Template_Attachment = trim($attachment_list[$i]);

                if (is_file(_UPLOAD_DIR.$email_Template_Attachment)) {

                    $attachment_arr[] = [
					'path' => _UPLOAD_DIR.$email_Template_Attachment,
					"name" => basename($email_Template_Attachment),
				    ];				
               }
            }

			$mailer->attachments = $attachment_arr;
			
						
    $mailer->send();
    return 1;
} catch (Exception $e) {
    echo "Mailer Error: " . $e->getMessage();
}

		}
	}
    
}

?>