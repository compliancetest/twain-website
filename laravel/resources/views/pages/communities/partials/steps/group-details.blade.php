{!! Form::label('title', 'Community Name (required): ') !!}
<br>
{!! Form::text('title', null, ['size' => '80', 'aria-required' => 'true']) !!}
<br>

{!! Form::label('description', 'Community Description (required): ') !!}
<br>
{!! Form::textarea('description', null, ['cols' => '80', 'aria-required' => 'true']) !!}
<br>


<div class="submit" id="previous-next">

    {!! Form::submit($submitButtonText,  ['class' => 'action-btn process-btn', 'id' => 'group-creation-create', 'name' => 'save']) !!}

    <div class="clear"></div>
</div>