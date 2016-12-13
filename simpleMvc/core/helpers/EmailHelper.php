<?php
 /*
 * This file is part of the SimpleMvc package.

 * @copyright 2016 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleMvc;

require_once (CORE_VENDORS . 'PHPMailer/PHPMailerAutoload.php');

class EmailHelper 
{
    /**
     * Send email with SMTP.

     * string $email_to : email to send.
     * string $email_title : email title.
     * string $form_file : email html form file.
     * array $data : data to use in email form.
     * array $smtpInfo : smtp info.
     * e. g. 
     * $smtpInfo = [
        //custom log types
        'Host' => 'smtp.simpleMvc.com',
        'Username' => 'jamie@test.com,
        'Password' => '123456aa',
        'Secure' => 'tls',
        'Port' => 587,
        'From' => 'simplemvc@test.com',
        'FromName' => 'SimpleMvc',
       ];    

     * @return boolean
     */
    public static function sendEmail($email_to, $email_title, $form_file, $data, $smtpInfo)
    {
        //check email validation
        if(!self::isValidEmail($email_to)) {
            return false;
        }
        
        //get PHPMailer
        $mail = new PHPMailer();

        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->CharSet = "UTF-8";
        $mail->addAddress($email_to); // Add a recipient
        $mail->addReplyTo($smtpInfo['From']);
        $mail->isHTML(true); // Set email format to HTML

        //SNMP info
        $mail->Host = $smtpInfo['Host']; // Specify main and backup SMTP servers
        $mail->Username = $smtpInfo['Username']; // SMTP username
        $mail->Password = $smtpInfo['Password']; // SMTP password
        $mail->SMTPSecure = $smtpInfo['Secure']; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $smtpInfo['Port']; // TCP port to connect to
        $mail->From = $smtpInfo['From'];
        $mail->FromName = $smtpInfo['FromName'];

        $mail->Subject = $email_title;
        $mail->Body = self::getFileContentsWithEval($form_file, $data);

        return $mail->send();
    }

    private static function getFileContentsWithEval($file, $data)
    {
        $search = array();
        $replace = array();
        
        foreach ($data as $key => $value) {
            array_push($search, '{' . $key . '}');
            array_push($replace, $value);
        }
        
        $forms = file_get_contents($file);
        $eval_contents = str_replace($search, $replace, $forms);
        
        return $eval_contents;
    }

    private static function isValidEmail($email)
    {
        $rtv = false;
        $regex = "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$";
        if (preg_match($regex, $email)) {
            $rtv = true;
        } else {
            $rtv = false;
        }
        
        return rtv;
    }
}