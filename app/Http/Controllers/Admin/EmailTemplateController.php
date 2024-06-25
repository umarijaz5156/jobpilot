<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminEmailTemplateRequest;
use App\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $email_templates = EmailTemplate::all();

            return view('backend.settings.pages.email-template', compact('email_templates'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function save(AdminEmailTemplateRequest $request)
    {
        try {
            $email_template_data = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            if (! empty($email_template_data['name'])) {
                $email_template_data['name'] = $request->name;
            }

            $email_template = ! empty($request->id) ? EmailTemplate::find($request->id) : null;

            if ($email_template) {
                $email_template = $email_template->update($email_template_data);
            } else {
                $email_template_data['type'] = $request->type;
                $email_template = EmailTemplate::create($email_template_data);
            }

            if ($email_template) {
                return back()->with('success', __('Email template saved!'));
            }

            return back()->with('error', __('Email template save failed!'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * format `$message` by `$type`
     *
     * @param  string  $message
     * @return mixed formatted data
     */
    public static function getFormattedTextByType(string $type, $data = null)
    {
        try {
            $type_data = self::getDataByType($type, $data);
            $formatter = self::getFormatterByType($type, $type_data);
            $email_template = EmailTemplate::where('type', $type)->first();
            $subject = optional($email_template)->subject ?? '';
            $message = optional($email_template)->message ?? '';

            return [
                'subject' => html_entity_decode(str_replace($formatter['search'], $formatter['replace'], $subject)),
                'message' => html_entity_decode(str_replace($formatter['search'], $formatter['replace'], $message)),
            ];
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * get data by type to be replaced by flags
     *
     * @param  string  $type  type of email template
     * @param  mixed  $data  any data passed
     * @return array
     */
    public static function getDataByType($type, $data = null)
    {
        try {
            $return_data = [];

            // if the type is among the following, key and value is the same
            if (in_array($type, ['new_edited_job_available', 'new_job_available', 'new_plan_purchase', 'new_user_registered', 'plan_purchase', 'new_pending_candidate', 'new_candidate', 'new_company_pending', 'new_company', 'update_company_pass', 'verify_subscription_notification'])) {
                $return_data = $data;
            }

            return $return_data;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * format flags with data
     *
     * @param  mixed  $data
     * @return array array with search and replace data
     */
    public static function getFormatterByType(string $type, $data = null)
    {
        try {
            $format = [];

            if ($type === 'new_edited_job_available') {
                $format['search'] = ['{admin_name}'];

                if ($data !== null) {
                    $format['replace'] = [$data['admin_name']];
                }
            }

            if ($type === 'new_job_available') {
                $format['search'] = ['{admin_name}'];

                if ($data !== null) {
                    $format['replace'] = [$data['admin_name']];
                }
            }

            if ($type === 'new_plan_purchase') {
                $format['search'] = ['{admin_name}', '{user_name}', '{plan_label}'];

                if ($data !== null) {
                    $format['replace'] = [$data['admin_name'], $data['user_name'], $data['plan_label']];
                }
            }

            if ($type === 'new_user_registered') {
                $format['search'] = ['{admin_name}', '{user_role}'];

                if ($data !== null) {
                    $format['replace'] = [$data['admin_name'], $data['user_role']];
                }
            }

            if ($type === 'plan_purchase') {
                $format['search'] = ['{user_name}', '{plan_type}'];

                if ($data !== null) {
                    $format['replace'] = [$data['user_name'], $data['plan_type']];
                }
            }

            // user candidate and company
            if (in_array($type, ['new_pending_candidate', 'new_candidate', 'new_company_pending', 'new_company'])) {
                $format['search'] = ['{user_name}', '{user_email}', '{user_password}'];

                if ($data !== null) {
                    $format['replace'] = [$data['user_name'], $data['user_email'], $data['user_password']];
                }
            }

            if ($type === 'update_company_pass') {
                $format['search'] = ['{user_name}', '{user_email}', '{password}', '{account_type}'];

                if ($data !== null) {
                    $format['replace'] = [$data['user_name'], $data['user_email'], $data['password'], $data['account_type']];
                }
            }

            if ($type === 'verify_subscription_notification') {
                $format['search'] = ['{verify_subscription}'];

                if ($data !== null) {
                    $format['replace'] = [$data['verify_subscription']];
                }
            }

            return $format;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
