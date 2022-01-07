<?php
 
namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use AWS;

use App\Models\{AutoResponder, User, NotificationLogs};  
 
trait AutoResponderTrait {
  
    public function get_template_by_name($name) {
        $template = AutoResponder::where('template_name', $name)->first(['id', 'template_name','subject','template']);
        return $template;
   } 
   public function getUserId($data) {
        $userid = User::where('email', $data)->orWhere('phone_number', $data)->orWhere('device_token', $data)->first('id');
        return $userid->id;
   } 

    public function send_mail($to, $subject, $email_body, $cc = NULL, $templet=NULL){
        
        $smtp_host = config('app.host');
        $smtp_port = config('app.port');
        $smtp_from_email = config('app.from_email');
        $smtp_username = config('app.username');
        $smtp_from_name = config('app.from_name');
        $smtp_password = config('app.password');
        $smtp_encryption = config('app.encryption');  
        
        // Create the Transport
        $transport = (new Swift_SmtpTransport($smtp_host, $smtp_port, $smtp_encryption ))
        ->setUsername($smtp_username)
        ->setPassword($smtp_password);
        
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);
        
        // Create a message
        $message = (new Swift_Message($subject))
        ->setFrom([$smtp_from_email => $smtp_from_name])
        ->setTo($to);
        if($cc)
        $message->setCc($cc);
        $message->setBody($email_body, 'text/html');

        // Send the message
        $result = $mailer->send($message);

        if($result){
            $userId = $this->getUserId($to); 
            $notificationdata = [
                'user_id' => $userId,
                'type' => 'email',
                'send_to' => $to,
                'message' => $templet, 
            ];
    
            NotificationLogs::create($notificationdata);
        }

        return $result;

    }

    public function generateOtp(){
        $otp = rand(100000,999999);
        // $otp = 111111;
        return $otp;
    }
    public function sendMessage($phone_number, $message){
        try {
            $sms = AWS::createClient('sns'); 
 
            $response = $sms->publish([
                'Message' => $message,
                'PhoneNumber' => $phone_number,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType'  => [
                        'DataType'    => 'String',
                        'StringValue' => 'Transactional',
                    ]
                ],
            ]);
            if($response){
                $userId = $this->getUserId($phone_number);
                $notificationdata = [
                    'user_id' => $userId,
                    'type' => 'sms',
                    'send_to' => $phone_number,
                    'message' => $message, 
                ];
        
                NotificationLogs::create($notificationdata);
            }
            return $response;
             
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        }  
    }
 
}