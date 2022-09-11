<?php

namespace App\Http\Controllers;

use App\Services\SendinBlue\SendinBlue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EmailSendinBlueController extends Controller
{
    public function sendEmailAttachment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['nullable'],
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
            'email' => ['required']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $file_path = null;
        $file = $request->file('file');
        if ($file) {
            $file_path = $this->getFile($file);
        }
        $details = [
            'title' => $request['title'],
            'body' => $request['body']
        ];
        $email_data = explode(',', $request['email']);
        $emails = array_values($email_data);
        \Mail::to($emails)->send(new \App\Mail\MyTestMail($details, $file_path));
        return responseSuccess('ایمیل با موفقیت ارسال گردید.', '');

    }

    private function storeFile($file, $file_name, $path)
    {
        return Storage::disk('public')->putFileAs(
            $path,
            $file,
            $file_name
        );
    }

    private function getFile($file)
    {
        $file_name = time() . $file->getClientOriginalName();
        $path = 'email/file/';
        $this->storeFile($file, $file_name, $path);
        Storage::disk('public')->get('email/file/' . $file_name);
        return $path . $file_name;
    }

    public function sendEmail(Request $request)
    {
        //Send an email campaign to your test list
        $validator = Validator::make($request->all(), [
            'campaignId' => ['required', 'integer'],
            'email' => ['required'],
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $campaignId = (int)$request['campaignId'];
        $email_data = explode(',', $request['email']);
        $emails = array_values($email_data);
        return SendinBlue::sendEmail($campaignId, $emails);
    }

    public function sendTransactionalEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string'],
            'headers' => ['required'],
            'params' => ['nullable'],
            'bcc' => ['nullable']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::sendTransactionalEmail($request['subject'], $request['headers'], $request['params'], $request['bcc']);
    }

    public function getCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaignId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $campaignId = (int)$request['campaignId'];
        return SendinBlue::getCampaign($campaignId);
    }

    public function deleteCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaignId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $campaignId = (int)$request['campaignId'];
        return SendinBlue::deleteCampaign($campaignId);
    }

    public function accountInformation()
    {
        //Get your account information, plan and credits details
        return SendinBlue::accountInformation();
    }

    public function getUpdateContacts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => ['required', 'integer'],
            'offset' => ['required', 'integer'],
            'date' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::getUpdateContacts($request['limit'], $request['offset'], $request['date']);
    }

    public function importContacts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer'],
            'file' => ['required'],
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $file = $request->file('file');
        $file_name = 'contacts' . '-' . date('Y-m-d') . '-' . $file->getClientOriginalName();
        $this->saveFile($file, $file_name);
        $file_body = Storage::disk('public')->get('sendinBlue/file/' . $file_name);
        return SendinBlue::importContacts($file_body, array((int)$request['listId']));
    }

    public function saveFile($file, $file_name)
    {
        $path = 'sendinBlue/file/';
        Storage::disk('public')->putFileAs($path, $file, $file_name);
        return $path . $file_name;
    }

    public function exportContacts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::exportContacts((int)$request['listId']);
    }

    public function createContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer'],
            'email' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $listId = array((int)$request['listId']);
        return SendinBlue::createContact($request['email'], $listId);
    }

    public function createContacts(Request $request)
    {
        //add existing contacts to the other list
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer'],
            'email' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $emails = explode(',', $request['email']);
        $listId = (int)$request['listId'];
        return SendinBlue::createContacts($emails, $listId);
    }

    public function deleteContact(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => ['required', 'string']]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $identifier = $request['email'];
        return SendinBlue::deleteContact($identifier);
    }

    public function deleteContacts(Request $request)
    {
        //remove existing contacts to the other list
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer'],
            'email' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $listId = (int)$request['listId'];
        $email_data = explode(',', $request['email']);
        $emails = array_values($email_data);
        return SendinBlue::deleteContacts($listId, $emails);
    }

    public function updateContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldEmail' => ['required', 'string'],
            'newEmail' => ['required', 'string'],
            'firstName' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::updateContact($request['oldEmail'], $request['newEmail'], $request['firstName']);
    }

    public function contactCampaignsStatistics(Request $request)
    {
        //Get email campaigns' statistics for a contact
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'startDate' => ['required', 'date_format:Y-m-d'],
            'endDate' => ['required', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::contactCampaignsStatistics($request['email'], $request['startDate'], $request['endDate']);
    }

    public function getAllList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => ['required', 'integer'],
            'offset' => ['required', 'integer'],
            'sort' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::getAllList($request['limit'], $request['offset'], $request['sort']);
    }

    public function createList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'folderId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::createList($request['name'], (int)$request['folderId']);
    }

    public function updateList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'listId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::updateList($request['name'], (int)$request['listId']);
    }

    public function deleteList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::deleteList((int)$request['listId']);
    }

    public function getContactsFromList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date_format:Y-m-d'],
            'limit' => ['required', 'integer'],
            'offset' => ['required', 'integer'],
            'listId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        $modifiedSince = new \DateTime($request['date'] . "T00:00:00+00:00");
        return SendinBlue::getContactsFromList((int)$request['listId'], $modifiedSince, (int)$request['limit'], (int)$request['offset']);
    }

    public function getFolders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => ['required', 'integer'],
            'offset' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::getFolders((int)$request['limit'], (int)$request['offset']);
    }

    public function deleteFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folderId' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::deleteFolder((int)$request['folderId']);
    }

    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::createFolder($request['name']);
    }

    public function createCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag' => ['nullable', 'string'],
            'sender_name' => ['required', 'string'],
            'name' => ['required', 'string'],
            'templateId' => ['nullable', 'integer'],
            'subject' => ['required', 'string'],
            'listIds' => ['required', 'array'],
            'exclusionListIds' => ['nullable', 'array'],
            'type' => ['nullable', 'string'],
            'header' => ['nullable', 'string'],
            'footer' => ['nullable', 'string'],
            'utmCampaign' => ['nullable', 'string'],
            'params_parameter' => ['nullable', 'string'],
            'params_address' => ['nullable', 'string'],
            'params_subject' => ['nullable', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::createCampaign($request['tag']
            , $request['sender_name']
            , $request['name']
            , $request['templateId']
            , $request['subject']
            , $request['listIds']
            , $request['exclusionListIds']
            , $request['type']
            , $request['header']
            , $request['footer']
            , $request['utmCampaign']
            , $request['params_parameter']
            , $request['params_address']
            , $request['params_subject']);
    }

    public function updateCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaignId' => ['required', 'integer'],
            'tag' => ['nullable', 'string'],
            'sender_name' => ['required', 'string'],
            'name' => ['required', 'string'],
            'templateId' => ['nullable', 'integer'],
            'subject' => ['required', 'string'],
            'listIds' => ['required', 'array'],
            'exclusionListIds' => ['nullable', 'array'],
            'type' => ['nullable', 'string'],
            'header' => ['nullable', 'string'],
            'footer' => ['nullable', 'string'],
            'utmCampaign' => ['nullable', 'string'],
            'params_parameter' => ['nullable', 'string'],
            'params_address' => ['nullable', 'string'],
            'params_subject' => ['nullable', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::updateCampaign($request['campaignId']
            , $request['tag']
            , $request['sender_name']
            , $request['name']
            , $request['templateId']
            , $request['subject']
            , $request['listIds']
            , $request['exclusionListIds']
            , $request['type']
            , $request['header']
            , $request['footer']
            , $request['utmCampaign']
            , $request['params_parameter']
            , $request['params_address']
            , $request['params_subject']);
    }

    public function createTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_name' => ['required', 'string'],
            'templateName' => ['required', 'string'],
            'text' => ['required', 'string'],
            'subject' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return responseError(null, $validator->errors(), 400);
        }
        return SendinBlue::createTemplate($request['sender_name'], $request['templateName'], $request['text'], $request['subject']);
    }
}
