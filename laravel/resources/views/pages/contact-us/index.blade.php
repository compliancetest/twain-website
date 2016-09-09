@extends('app')

@section('content')
    <div class="container main-container">
        <div class="main-content">
            <div class="page-title">
                <h1>Contact Us</h1>
            </div>

            <div class="row margin_top20">
                <div class="col-md-12">
                    Thank you for your interest in the Drummond Group TWAIN test platform. Please contact us via the phone numbers or contact info below.
                </div>
            </div>

            <div class="red_text_section">
                <h3>Get in Touch</h3>
                <hr class="red_hr">
            </div>

            <div class="row">
                <div class="col-md-4">
                    <b>Drummond Group Inc.</b><br>
                    13359 North Hwy 183,<br>
                    Suite B-406-238 Austin,<br>
                    TX 78750, USA<br>
                    Phone: <b>+1 817-294-7339</b><br>
                    Email: <a href="mailto:info2@drummondgroup.com">info2@drummondgroup.com</a><br>
                </div>

                <div class="col-md-12 margin_top20">
                    Please type a message and provide an email address that we can respond to.
                </div>
            </div>

            {{ Form::open(['name' => 'contact-us-form', 'method' => 'post', 'url' => getSiteUrl() . '/contact-us/', 'class' => 'margin_top20']) }}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', 'Name *') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'You name']) !!}

                        @if($errors->get('name'))
                            <div class="alert alert-danger">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', 'E-mail *') !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Your email address']) !!}
                        @if($errors->get('email'))
                            <div class="alert alert-danger">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        {!! Form::label('phone', 'Phone number') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Your phone number']) !!}
                        @if($errors->get('phone'))
                            <div class="alert alert-danger">
                                {{ $errors->first('phone') }}
                            </div>
                        @endif
                    </div>

                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('phone', 'Message') !!}
                        {!! Form::textarea('message_text', null, ['cols' => '101', 'rows' => 11, 'placeholder' => 'Message']) !!}
                        @if($errors->get('message_text'))
                            <div class="alert alert-danger">
                                {{ $errors->first('message_text') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-4">
                    {!! Recaptcha::render() !!}
                    @if($errors->get('g-recaptcha-response'))
                        <div class="alert alert-danger">
                            {{ 'Recaptcha field is required' }}
                        </div>
                    @endif

                </div>

                <div class="col-md-1 col-md-offset-7">
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Send</button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

@stop