<?php

function responseError($message = 'خطا در اطلاعات ورودی', $errors, $status = 400)
{
    if ($message == '') {
        $message = 'خطا در اطلاعات ورودی';
    }
    if (!$errors) {
        return ['status' => (int)$status, 'message' => $message];
    }
    return ['errors' => $errors, 'status' => (int)$status, 'message' => $message];
}

function responseSuccess($message = 'عملیات با موفقیت انجام شد', $data = null)
{
    if ($message == '') {
        $message = 'عملیات با موفقیت انجام شد';
    }

    if ($data) {
        return ['message' => $message, 'data' => $data, 'status' => 200];
    }
    return ['message' => $message, 'status' => 200];
}

function responseServerError($errors = null)
{
    return response(['message' => 'خطای داخلی سرور', 'errors' => $errors, 'status' => 500], 500);
}
