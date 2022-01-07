<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutoResponder;

class AutoResponderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *#009a73
     * @return void
     */
    public function run()
    {
        $mailTemplet = AutoResponder::where('template_name', 'SET_PASSWORD')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'SET_PASSWORD',
                'template' => '<center style="width: 100%; background-color: #fff;">
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <table style="margin: auto;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td class="bg_white" style="padding: 1.2em 2.5em 1em 2.5em; background-color: #009a73;" valign="top">
                <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td class="logo" style="text-align: center; color: #fff;">&nbsp;Q & Go Assistant</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr -->
                <tr style="border-left: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
                <td class="intro bg_white" style="padding: 2em 0;" valign="middle">
                <table>
                <tbody>
                <tr>
                <td>
                <div class="text" style="padding: 0 2.5em;">
                <h2 style="text-align: left; color: #000000; font-size: 18px; margin-top: 0; line-height: 1.4; font-weight: bold;">Hello {{$name}},</h2>
                <p style="text-align: left; margin-top: 15px;">You have recently register your account with Q & Go. Click on this link to set your password.</p>
                <p style="margin-top: 15px;"><a class="btn btn-black" style="background-color: #009a73; padding: 5px 20px; color: #fff;" href="{{$token}}">Click here</a></p>
                <p style="text-align: left; margin: 0px;">Or Copy the below link on your browser tab :</p>
                <p style="text-align: left;">{{$token}}</p>
                <p style="text-align: left;">If you did not initiate this request, please ignore this mail and the link will soon expire automatically.</p>
                <p style="margin: 10px 0 0; text-align: left; color: #000000;">Thanks</p>
                <p style="margin: 0px; text-align: left; color: #000000; font-weight: bold;">Q & Go Assistant Team</p>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr --> <!-- 1 Column Text + Button : END --></tbody>
                </table>
                <table style="margin: auto; background: #fafafa;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td style="padding: 2px 200px 10px;">
                <table>
                <tbody>
                <tr>
                <td style="text-align: center; margin: 0;">
                <p style="margin: 0; font-size: 12px;">&copy; 2021 <a style="color: #141637;">Q & Go Assistant</a>. All Rights Reserved</p>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </div></center>',
                'subject' => 'Reset Password',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'FORGOT_PASSWORD')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'FORGOT_PASSWORD',
                'template' => '<center style="width: 100%; background-color: #fff;">
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <table style="margin: auto;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td class="bg_white" style="padding: 1.2em 2.5em 1em 2.5em; background-color: #009a73;" valign="top">
                <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td class="logo" style="text-align: center; color: #fff;">&nbsp;Q & Go Assistant</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr -->
                <tr style="border-left: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
                <td class="intro bg_white" style="padding: 2em 0;" valign="middle">
                <table>
                <tbody>
                <tr>
                <td>
                <div class="text" style="padding: 0 2.5em;">
                <h2 style="text-align: left; color: #000000; font-size: 18px; margin-top: 0; line-height: 1.4; font-weight: bold;">Hello {{$name}},</h2>
                <p style="text-align: left; margin-top: 15px;">You have recently requested to reset your password for your account. Click on this link to reset your password.</p>
                <p style="margin-top: 15px;"><a class="btn btn-black" style="background-color: #009a73; padding: 5px 20px; color: #fff;" href="{{$token}}">Click here</a></p>
                <p style="text-align: left; margin: 0px;">Or Copy the below link on your browser tab :</p>
                <p style="text-align: left;">{{$token}}</p>
                <p style="text-align: left;">If you did not initiate this request, please ignore this mail and the link will soon expire automatically.</p>
                <p style="margin: 10px 0 0; text-align: left; color: #000000;">Thanks</p>
                <p style="margin: 0px; text-align: left; color: #000000; font-weight: bold;">Q & Go Assistant Team</p>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr --> <!-- 1 Column Text + Button : END --></tbody>
                </table>
                <table style="margin: auto; background: #fafafa;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td style="padding: 2px 200px 10px;">
                <table>
                <tbody>
                <tr>
                <td style="text-align: center; margin: 0;">
                <p style="margin: 0; font-size: 12px;">&copy; 2021 <a style="color: #141637;">Q & Go Assistant</a>. All Rights Reserved</p>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </div></center>',
                'subject' => 'Reset Password',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VERIFY_EMAIL')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VERIFY_EMAIL',
                'template' => '<center style="width: 100%; background-color: #fff;">
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <table style="margin: auto;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td class="bg_white" style="padding: 1.2em 2.5em 1em 2.5em; background-color: #009a73;" valign="top">
                <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td class="logo" style="text-align: center; color: #fff;">&nbsp;Q & Go Assistant</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr -->
                <tr style="border-left: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
                <td class="intro bg_white" style="padding: 2em 0;" valign="middle">
                <table>
                <tbody>
                <tr>
                <td>
                <div class="text" style="padding: 0 2.5em;">
                <h2 style="text-align: left; color: #000000; font-size: 18px; margin-top: 0; line-height: 1.4; font-weight: bold;">Hello {{$name}},</h2>
                <p style="text-align: left; margin-top: 15px; margin-bottom: 0;">Welcome to Q & Go Assistant</p>
                <p style="text-align: left; margin: 0;">Thank you for signing up, we are excited to have you on board. Please activate your account by clicking on the following link and get verified.</p>
                <p style="margin-top: 15px;"><a class="btn btn-black" style="background-color: #009a73; padding: 5px 20px; color: #fff;" href="{{$token}}">Click here</a></p>
                <p style="text-align: left; margin: 0px;">Or click the direct link :</p>
                <p style="text-align: left;">{{$token}}</p>
                <p style="text-align: left;">Once that is completed from your end, your account will be activated.</p>
                <p style="text-align: left;">We are here to provide you with tons of great resources to help you get set up for success. But if you have any queries regarding your account, please mail us right away. We will be pleased to assist you.</p>
                <p style="margin: 10px 0 0; text-align: left; color: #000000;">Thanks</p>
                <p style="margin: 0px; text-align: left; color: #000000; font-weight: bold;">Q & Go Assistant Team</p>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr --> <!-- 1 Column Text + Button : END --></tbody>
                </table>
                <table style="margin: auto; background: #fafafa;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td style="padding: 2px 200px 10px;">
                <table>
                <tbody>
                <tr>
                <td style="text-align: center; margin: 0;">
                <p style="margin: 0; font-size: 12px;">&copy; 2021 <a style="color: #141637;">Q & Go Assistant</a>. All Rights Reserved</p>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </div>
                </center>',
                'subject' => 'Welcome to Q & Go Assistant, Verify your email',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'CONTACT_US')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'CONTACT_US',
                'template' => '<center style="width: 100%; background-color: #fff;">
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <table style="margin: auto;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td class="bg_white" style="padding: 1.2em 2.5em 1em 2.5em; background-color: #009a73;" valign="top">
                <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td class="logo" style="text-align: center; color: #fff;">&nbsp;Q & Go Assistant</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr -->
                <tr style="border-left: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
                <td class="intro bg_white" style="padding: 2em 0;" valign="middle">
                <table>
                <tbody>
                <tr>
                <td>
                <div class="text" style="padding: 0 2.5em;">
                <h2 style="text-align: left; color: #000000; font-size: 18px; margin-top: 0; line-height: 1.4; font-weight: bold;">Hello {{$name}},</h2>
                <p style="text-align: left; margin-top: 15px;">You have received this mail as a user who has shown interest in Q & Go Assistant.</p>
                <p style="text-align: left; margin-top: 15px;">Below are the details of the user.</p>
                <p style="text-align: left; margin-top: 15px;"><strong>Name: </strong>{{$username}}</p>
                <p style="text-align: left; margin-top: 15px;"><strong>Email: </strong>{{$email}}</p>
                <p style="text-align: left; margin-top: 15px;"><strong>Mobile: </strong>{{$mobile}}</p>
                <p style="text-align: left; margin-top: 15px;"><strong>Mesage: </strong>{{$user_message}}</p>
                <p style="margin: 10px 0 0; text-align: left; color: #000000;">Thanks</p>
                <p style="margin: 0px; text-align: left; color: #000000; font-weight: bold;">Q & Go Assistant Team</p>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr --> <!-- 1 Column Text + Button : END --></tbody>
                </table>
                <table style="margin: auto; background: #fafafa;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td style="padding: 2px 200px 10px;">
                <table>
                <tbody>
                <tr>
                <td style="text-align: center; margin: 0;">
                <p style="margin: 0; font-size: 12px;">&copy; 2021 <a style="color: #141637;">Q & Go Assistant</a>. All Rights Reserved</p>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </div>
                </center>',
                'subject' => 'New user contact you via website',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'ORDER_STATUS')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'ORDER_STATUS',
                'template' => '<center style="width: 100%; background-color: #fff;">
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <table style="margin: auto;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td class="bg_white" style="padding: 1.2em 2.5em 1em 2.5em; background-color: #009a73;" valign="top">
                <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td class="logo" style="text-align: center; color: #fff;">&nbsp;Q & Go Assistant</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr -->
                <tr style="border-left: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
                <td class="intro bg_white" style="padding: 2em 0;" valign="middle">
                <table>
                <tbody>
                <tr>
                <td>
                <div class="text" style="padding: 0 2.5em;">
                <h2 style="text-align: left; color: #000000; font-size: 18px; margin-top: 0; line-height: 1.4; font-weight: bold;">Hello {{$name}},</h2>
                <p style="text-align: left; margin-top: 15px;">{{$message}}</p>  
                <p style="text-align: left;">If you did not initiate this request, please ignore</p>
                <p style="margin: 10px 0 0; text-align: left; color: #000000;">Thanks</p>
                <p style="margin: 0px; text-align: left; color: #000000; font-weight: bold;">Q & Go Assistant Team</p>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                <!-- end tr --> <!-- 1 Column Text + Button : END --></tbody>
                </table>
                <table style="margin: auto; background: #fafafa;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                <tr>
                <td style="padding: 2px 200px 10px;">
                <table>
                <tbody>
                <tr>
                <td style="text-align: center; margin: 0;">
                <p style="margin: 0; font-size: 12px;">&copy; 2021 <a style="color: #141637;">Q & Go Assistant</a>. All Rights Reserved</p>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </div></center>',
                'subject' => 'Order Status',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'CUSTOMER_REGISTER')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'CUSTOMER_REGISTER',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-cus-register.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Next time, make ordering even easier.</h1>
                                                                            <p class="lead">Click the link below to finish setting up your free Q&GO account.
                                                                                Get a range of benefits including push notifications, and
                                                                                updates from your favourite providers.
                                                                            </p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <a href="#"><img src="{{img-path}}/btn-cus-register.jpg" alt finish creating your account></a>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/app-promo.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row darkband">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-6 columns first">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top:10px;"><a href="#"><img src="{{img-path}}/icon-app-store.jpg" alt="Download from App Store" style="float:right;"></a></p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                        <th class="small-12 large-6 columns last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top: 10px;">
                                                                                <a href="#"><img src="{{img-path}}/icon-play-store.jpg" alt="Download from Google Play"></a>
                                                                            </p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Welcome customer',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'CUSTOMER_WELCOME')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'CUSTOMER_WELCOME',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-cus-welcome.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <h1>Congratulations — your account has been created.</h1>
                                                                            <p class="lead">
                                                                                To update your account details or scan codes at participating vendors, simply log in via our app.
                                                                            </p>
                                                                            <p class="lead">If you haven’t already done so, download the Q&GO app and make ordering even easier.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/app-promo.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row darkband">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-6 columns first">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top:10px;"><a href="#"><img src="{{img-path}}/icon-app-store.jpg" alt="Download from App Store" style="float:right;"></a></p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                        <th class="small-12 large-6 columns last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top: 10px;">
                                                                                <a href="#"><img src="{{img-path}}/icon-play-store.jpg" alt="Download from Google Play"></a>
                                                                            </p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Welcome to Q & Go Assistant, Verify your email',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_REGISTER')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_REGISTER',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-register.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <p class="lead">Thanks for signing up. We just need to verify your email address in order to set up your account.</p>
                                                                            <p class="lead">To start your 14-day free trial click the link below and verify your account:</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <a href="#"><img src="{{img-path}}/btn-finish.jpg"></a>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p class="lead">We look forward to helping you streamline your queue with Q&amp;GO.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/app-promo.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row darkband">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-6 columns first">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top:10px;"><a href="#"><img src="{{img-path}}/icon-app-store.jpg" alt="Download from App Store" style="float:right;"></a></p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                        <th class="small-12 large-6 columns last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <p style="padding-top: 10px;">
                                                                                <a href="#"><img src="{{img-path}}/icon-play-store.jpg" alt="Download from Google Play"></a>
                                                                            </p>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Welcome to Q & Go Assistant, Verify your email',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_WELCOME')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_WELCOME',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-welcome.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*, it’s great to have you here.</h1>
                                                                            <p class="lead">Thank you for registering your vendor account with Q&GO! You can now take advantage of all the features and benefits that Q&GO has to offer including:</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/features.jpg" alt="Features">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Your 14-day free trial starts today!</h1>
                                                                            <p class="lead">If you are looking for more information on how to use and manage your Q&GO app, see our getting started guide below. We look forward to helping you streamline your queue with Q&GO.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Welcome to Q & Go Assistant, Verify your email',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_REMINDER_SUBSCRIPTION')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_REMINDER_SUBSCRIPTION',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-reminder.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <p class="lead">This is a courtesy email to inform you that your next payment for your Q&GO subscription will be charged to your nominated card on the *DATE*.
                                                                            </p>
                                                                            <p class="lead">We hope you are enjoying all the benefits and features of Q&amp;GO.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src=""{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src=""{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src=""{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src=""{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Q & Go subscription reminder',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_OVERDUE')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_OVERDUE',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-overdue.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <p class="lead">There appears to have been a problem with your subscription. Please log into your vendor console and update your payment details to avoid interruption to your Q&GO service.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <a href="#"><img src="{{img-path}}/btn-vendor-login.jpg"></a>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Q & Go overdue',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_SUSPENDED')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_SUSPENDED',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-suspended.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <p class="lead">We suspended your subscription with Q&GO, it looks like your latest payment was unsuccessful, please log into your vendor console and update your details to restore your subscription.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <a href="#"><img src="{{img-path}}/btn-vendor-login.jpg"></a>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => ' Q & Go vendor suspended',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_CREDIT_LOW')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_CREDIT_LOW',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-credit-low.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi *NAME*,</h1>
                                                                            <p class="lead">It looks like your SMS credit is running out! Please log into your vendor console and top up your account to continue using our SMS notification feature.</p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <a href="#"><img src="{{img-path}}/btn-vendor-login.jpg"></a>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Q & Go credit low alert',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $mailTemplet = AutoResponder::where('template_name', 'VENDOR_TOPUP_SUCCESS')->count();
        if($mailTemplet == 0){
            AutoResponder::create([
                'template_name' => 'VENDOR_TOPUP_SUCCESS',
                'template' => <<<STRING
                <table class="body">
                <tr>
                    <td class="center" align="center" valign="top">
                        <center>
                            <table align="center" class="container float-center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <img src="{{img-path}}/hd-vendor-topup-success.jpg">
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <th class="small-12 large-12 columns first last">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>
                                                                            <h1>Hi {{name}},</h1>
                                                                            <p class="lead">
                                                                                You have successfully added 
                                                                                <amount> to your SMS credit. Remember if you haven’t done so already, you can activate the automatic top-up feature to avoid and
                                                                                    SMS disruptions. 
                                                                                </amount>
                                                                            </p>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table align="center" class="container lightband">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/title-get-started.jpg" alt="See Our Getting Started Guide" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <a href="#">
                                                                            <center><img src="{{img-path}}/btn-get-started.jpg" alt="Click Here" align="center" class="float-center"></center>
                                                                        </a>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="20" style="font-size:20px;line-height:20px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row">
                                                                <tbody>
                                                                    <tr>
                                                                        <center><img src="{{img-path}}/vendor-screens.jpg" alt="Interface Images" align="center" class="float-center"></center>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="spacer">
                                                                <tbody>
                                                                    <tr>
                                                                        <td height="32" style="font-size:32px;line-height:32px;">&nbsp;</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="spacer">
                                                <tbody>
                                                    <tr>
                                                        <td height="16" style="font-size:16px;line-height:16px;">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="row">
                                                <tbody>
                                                    <tr>
                                                        <center>
                                                            <img src="{{img-path}}/logo-vendors.jpg" alt="Q and Go Vendors" align="center" class="float-center">
                                                        </center>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center>
                                                <table align="center" class="menu float-center">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th class="menu-item float-center"><a href="#">Terms</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Privacy</a></th>
                                                                            <th class="menu-item float-center"><a href="#">Unsubscribe</a></th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- prevent Gmail on iOS font size manipulation -->
            <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
            STRING,
                'subject' => 'Q & Go top-up confirmation',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
