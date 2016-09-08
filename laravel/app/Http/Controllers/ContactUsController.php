<?php

namespace App\Http\Controllers;

use App\Http\Requests;
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
        Mail::send('emails.contactus', $request->all(), function ($message) use ($request) {
            $message->from('support@twain.gosource.com.au', 'Support');
            $message->subject('TWAIN Contact: ' . $request->get('name'));
            $message->to('info2@drummondgroup.com');
        });
        addMessage('Your message was sent successfully. Thanks.');
        return redirect('contact-us');
    }
}
