@if($errors->any())

    <div id="messages-wrapper" class="bp-template-notice">

        @foreach($errors->all() as $error)
            <p class="message error">{{ $error  }}</p>
        @endforeach

    </div>

@endif