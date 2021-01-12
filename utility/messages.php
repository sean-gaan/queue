<?php

function success($data, $code = 200)
{
    return message($data, $code);
}

function failure($data, $code = 400)
{
    return message($data, $code, 0);
}

function message($data, $code, $success = 1)
{
    return response(['success' => $success, 'data' => $data], $code);
}
