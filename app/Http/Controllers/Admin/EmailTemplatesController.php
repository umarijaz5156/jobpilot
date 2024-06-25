<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $mail_templates = EmailTemplate::all();

            return view('backend.email-template.index', compact('mail_templates'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'type' => 'required|unique:email_templates,type',
            'message' => 'required|string',
        ]);

        try {
            $email_template_data = [
                'name' => $request->name ?? '',
                'subject' => $request->subject ?? '',
                'type' => $request->type ?? '',
                'message' => $request->message ?? '',
            ];

            $email_template = null;

            if (isset($request->id)) {
                $email_template = EmailTemplate::where('type', $request->id)->first();

                if ($email_template) {
                    $email_template = $email_template->update($email_template_data);
                }
            } else {
                $email_template = EmailTemplate::create($email_template_data);
            }

            if ($email_template) {
                return back()->with(__('Template create success'));
            }

            return back()->withErrors(__('Template create failed'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Get formatted email subject and message by EmailTemplate type
     *
     * @param  mixed  $data
     * @return array An array with formatted "subject" and "message".
     */
    public static function formatMessage(string $type, $data)
    {
        try {
            $template = EmailTemplate::where('type', $type)->first();

            if (! $template) {
                \Log::error("No Email template of type $type not found.");

                return false;
            }

            $template = $template;
            $subject = $template->subject;
            $message = $template->message;

            $format = self::getFormatByType($type, $data);
            $search_format_arr = $format['search'];
            $replace_format_arr = $format['replace'];

            return [
                'subject' => str_replace($search_format_arr, $replace_format_arr, $subject),
                'message' => str_replace($search_format_arr, $replace_format_arr, $message),
            ];
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Get formatter array by type
     *
     * @param  string  $type  Type of mail template
     * @param  mixed|null  $data  Data to be replaced by
     * @return array Array of search and replace data
     *
     * @note It will only return search array if no $data provided
     * @note Register necessary formats here
     */
    public static function getFormatByType(string $type, $data = null)
    {
        try {
            $format = [];

            // new candidate
            if ($type == 'new_user') {
                $format['search'] = ['{user_name}', '{company_name}'];

                if ($data) {
                    $format['replace'] = [$data->name, config('name')];
                }
            }

            // new candidate
            if ($type == 'new_user') {
                $format['search'] = ['{user_name}', '{company_name}'];

                if ($data) {
                    $format['replace'] = [$data->name, config('name')];
                }
            }

            return $format;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
