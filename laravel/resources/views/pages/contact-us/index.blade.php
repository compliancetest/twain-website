@extends('app')

@section('content')
    <div class="container main-container">
        <div class="main-content contact-us">
            <div class="page-title">
                <h1>Contact Us</h1>
            </div>

            <p class="contact-us-description">Thank you for your interest in the Drummond Group TWAIN test platform. Please contact us via the phone numbers or contact info below.</p>

            <h3 class="contact-us-subtitle">Get in Touch</h3>

            <div class="contact-us-info-block">
                <strong>Drummond Group Inc.</strong><br>
                <address>
                13359 North Hwy 183,<br>
                Suite B-406-238 Austin,<br>
                TX 78750, USA<br>
                </address>
                Phone: <b>+1 817-294-7339</b><br>
                Email: <a href="mailto:info2@drummondgroup.com">info2@drummondgroup.com</a><br>
            </div>


            <p class="contact-us-form-description">Please type a message and provide an email address that we can respond to.</p>

            {{ Form::open(['name' => 'contact-us-form', 'method' => 'post', 'url' => getSiteUrl() . '/contact-us/', 'class' => 'margin_top20']) }}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group @if($errors->get('name')) has-error @endif">
                        {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'You name']) !!}

                        @if($errors->get('name'))
                            <span class="help-block">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="form-group @if($errors->get('email')) has-error @endif">
                        {!! Form::label('email', 'E-mail', ['class' => 'control-label required']) !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Your email address']) !!}
                        @if($errors->get('email'))
                            <span class="help-block">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="form-group @if($errors->get('phone')) has-error @endif">
                        {!! Form::label('phone', 'Phone number', ['class' => 'control-label']) !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Your phone number']) !!}
                        @if($errors->get('phone'))
                            <span class="help-block">
                                {{ $errors->first('phone') }}
                            </span>
                        @endif
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group @if($errors->get('message_text')) has-error @endif">
                        {!! Form::label('message_text', 'Message', ['class' => 'control-label required']) !!}
                        {!! Form::textarea('message_text', null, ['cols' => '101', 'rows' => 6, 'placeholder' => 'Message', 'class'=>'form-control']) !!}
                        @if($errors->get('message_text'))
                            <span class="help-block">
                                {{ $errors->first('message_text') }}
                            </span>
                        @endif
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group @if($errors->get('g-recaptcha-response')) has-error @endif">
                        <div class="pull-left">
                            <label class="control-label required">Capture validation</label>
                            {!! Recaptcha::render() !!}
                            @if($errors->get('g-recaptcha-response'))
                                <span class="help-block">
                                {{ 'Captcha validation is required' }}
                            </span>
                            @endif
                        </div>
                    </div>

                </div>


            </div>

            <div class="form-group">
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Send</button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

@stop