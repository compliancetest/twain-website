<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\WpOptions;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function index()
    {
        return view('pages.contact-us.index');
    }

    /**
     * Send submitted data
     * @param Requests\ContactUsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send(Requests\ContactUsRequest $request)
    {
        $contactUsEmail = WpOptions::where('option_name', 'tw_contact_us_email')->first()->option_value;
        Mail::send('emails.contactus', $request->all(), function ($message) use ($request, $contactUsEmail) {
            $message->from('support@twain.gosource.com.au', 'Drummond Group Support');
            $message->subject('Drummond Group TWAIN Testing Platform Contact: ' . $request->get('name'));
            $message->to($contactUsEmail);
        });
        addMessage('Your message was sent successfully. Thanks.');
        return redirect('contact-us');
    }
}
