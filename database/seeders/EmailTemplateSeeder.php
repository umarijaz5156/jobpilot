<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fetch the app name
        $appName = config('app.name');

        $email_templates = [
            [
                'name' => 'New User',
                'type' => 'new_user',
                'subject' => 'Welcome {user_name}',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hi {user_name},</strong></p><p>Welcome to <strong>{company_name}</strong>. It's great to have you here!</p><p>Have a great time!</p><p>Best Regards,<br><strong>{company_name}</strong> team</p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'Edited Job',
                'type' => 'new_edited_job_available',
                'subject' => 'New Edited Job Available For Approval!',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {admin_name}</strong>,<br>A new edited job available for approval!</p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Job Available',
                'type' => 'new_job_available',
                'subject' => 'New Job Available For Approval!',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {admin_name}</strong>,<br>A new job available for approval!</p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Plan Purchase',
                'type' => 'new_plan_purchase',
                'subject' => '{user_name} Has Purchased The {plan_label} Plan!',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>{user_name}</strong> Has Purchased The <strong>{plan_label}</strong> Plan!</p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New User Registered',
                'type' => 'new_user_registered',
                'subject' => 'New {user_role} Registered!',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {admin_name}</strong>,<br>A <strong>{user_role}</strong> Registered Recently!</p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'Plan Purchase',
                'type' => 'plan_purchase',
                'subject' => 'Plan Purchased',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name}!</strong><br>You purchase of <strong>{plan_type}</strong> has been successfully completed!<br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Pending Candidate',
                'type' => 'new_pending_candidate',
                'subject' => 'Candidate Created',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name}</strong>,<br><br>Your candidate profile has been created and is waiting for admin approval.<br><br>Please login with your credentials below to check status -<br><strong>Your Email :</strong> {user_email}<br><strong>Your Password :</strong> {user_password}<br><br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Candidate',
                'type' => 'new_candidate',
                'subject' => 'Candidate Created',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name}</strong>,<br><br>Your candidate profile has been created.<br><br>Please login with your credentials below to check status -<br><strong>Your Email : </strong>{user_email}<br><strong>Your Password : </strong>{user_password}<br><br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Company Pending',
                'type' => 'new_company_pending',
                'subject' => 'Company created and waiting for admin approval',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name}</strong>,<br><br>Your company profile has been created and is waiting for admin approval.<br><br>Please check back your account with the login information below -<br><strong>Your Email :</strong> {user_email}<br><strong>Your Password :</strong> {user_password}<br><br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'New Company',
                'type' => 'new_company',
                'subject' => 'Company Created',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name},</strong><br><br>Your company profile has been created. Please login with below information.<br><br>Please check back your account with the login information below -<br><strong>Your Email :</strong> {user_email}<br><strong>Your Password :</strong> {user_password}<br><br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'Update Company Password',
                'type' => 'update_company_pass',
                'subject' => '{account_type} Updated',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p><strong>Hello {user_name}</strong>,<br><br>Your <strong>{account_type}</strong> profile password updated.<br><br><strong>Your Email :</strong> {user_email}<br><strong>Your password :</strong> {password}<br><br>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
            [
                'name' => 'Verify Subscription Notification',
                'type' => 'verify_subscription_notification',
                'subject' => 'Verify Your Subscription',
                'message' => "<div style='box-sizing:border-box;font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:center; background-color:#edf2f7; color:#000; margin:0;padding:20px ;width:100%'><div style='color:#000; margin-bottom: 20px;'><strong>$appName</strong></div><div style='background:#fff; background-color:#fff; color:#718096; font-family:Helvetica,Arial,sans-serif; font-size:16px; text-align:left; max-width:600px; margin: 0 auto 20px; border: 1px solid #e5e4e6; border-radius: 10px; padding: 20px;'><p>Thanks for your interest in our newsletter!</p><p>You're one step away</p><h2>Verify your email address</h2><p>to subscribe our newsletter.</p><h3><a href='{verify_subscription}'>Verify Now</a>&nbsp;</h3><p>Best Regards<br><strong>$appName</strong></p></div><small>© ".date('Y')." $appName. All rights reserved.</small></div>",
            ],
        ];

        foreach ($email_templates as $email_template) {
            EmailTemplate::create($email_template);
        }

    }
}
