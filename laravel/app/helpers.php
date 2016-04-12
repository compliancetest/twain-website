<?php
function is_selected($v1, $v2)
{
    return $v1 == $v2 ? 'selected="selected"' : '';
}

function is_readonly($status = false)
{
    return $status ? 'readonly="readonly"' : '';
}

function is_disabled($status = false)
{
    return $status ? 'disabled="disabled"' : '';
}