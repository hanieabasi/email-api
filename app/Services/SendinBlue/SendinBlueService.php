<?php

namespace App\Services\SendinBlue;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use SendinBlue\Client\Api\AccountApi;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\SendTestEmail;
use SendinBlue\Client\Model\UpdateContact;
use SendinBlue\Client\Model\UpdateEmailCampaign;

class SendinBlueService
{
    public $apiContacts;
    public $apiEmailCampaigns;
    public $apiAccount;
    public $apiTransactional;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', config('services.sendinBlue.key_pakat'));
        $this->apiContacts = new ContactsApi(new Client(), $config);
        $this->apiEmailCampaigns = new EmailCampaignsApi(new Client(), $config);
        $this->apiAccount = new AccountApi(new Client(), $config);
        $this->apiTransactional = new TransactionalEmailsApi(new Client(), $config);
    }

    public function sendEmail($campaignId, $emails)
    {
        $emailTo = new SendTestEmail();
        $emailTo['emailTo'] = $emails;
        try {
            $result = $this->apiEmailCampaigns->sendTestEmail($campaignId, $emailTo);
            return responseSuccess('', json_encode($result));
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function sendTransactionalEmail($subject, $headers, $params, $bcc)
    {
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
        $sendSmtpEmail['subject'] = $subject;
        $sendSmtpEmail['htmlContent'] = '<html><body><h1>This is a transactional email test</h1></body></html>';
        $sendSmtpEmail['sender'] = array('name' => 'John Doe', 'email' => config('services.sendinBlue.sender'));
        //'to' is required
        $sendSmtpEmail['to'] = array(
            array('email' => config('services.sendinBlue.sender'), 'name' => 'sender')
        );
        $sendSmtpEmail['bcc'] = $bcc;
        $sendSmtpEmail['replyTo'] = array('email' => 'replyto@domain.com', 'name' => 'John Doe');
        $sendSmtpEmail['headers'] = array('Some-Custom-Name' => $headers);
        $sendSmtpEmail['params'] = array('parameter' => 'My param value', 'subject' => $params);
        try {
            $result = $this->apiTransactional->sendTransacEmail($sendSmtpEmail);
            return responseSuccess('', json_decode($result));
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function getCampaign($campaignId)
    {
        try {
            $result = $this->apiEmailCampaigns->getEmailCampaign($campaignId);
            return responseSuccess('', json_decode($result));
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function deleteCampaign($campaignId)
    {
        try {
            $this->apiEmailCampaigns->deleteEmailCampaign($campaignId);
            return responseSuccess('کمپین با موفقیت حذف شد', ['campaignId' => $campaignId]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function accountInformation()
    {
        try {
            $result = $this->apiAccount->getAccount();
            $result_data[] = array(
                'email' => $result->getEmail(),
                'firstName' => $result->getFirstName(),
                'lastName' => $result->getLastName(),
                'companyName' => $result->getCompanyName()
            );
            return responseSuccess('', $result_data);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function getUpdateContacts($limit, $offset, $modifiedSince)
    {
        try {
            $result = $this->apiContacts->getContacts($limit, $offset, $modifiedSince);
            $result_data[] = array(
                'count' => $result->getCount(),
                'list' => $result->getContacts()
            );
            return responseSuccess($result_data, '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function importContacts($file_body, $listId)
    {
        $requestContactImport = new \SendinBlue\Client\Model\RequestContactImport();
        $requestContactImport['fileBody'] = $file_body;
        $requestContactImport['listIds'] = $listId;
        $requestContactImport['emailBlacklist'] = false;
        $requestContactImport['smsBlacklist'] = false;
        $requestContactImport['updateExistingContacts'] = true;
        $requestContactImport['emptyContactsAttributes'] = false;
        try {
            $this->apiContacts->importContacts($requestContactImport);
            return responseSuccess('', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function exportContacts($listId)
    {
        $requestContactExport = new \SendinBlue\Client\Model\RequestContactExport();
        $requestContactExport['exportAttributes'] = ['email'];
        $requestContactExport['customContactFilter'] = array('actionForContacts' => 'allContacts', 'listId' => $listId);
        $requestContactExport['notifyUrl'] = 'https://hanieabc6@gmail.com';
        try {
            $this->apiContacts->requestContactExport($requestContactExport);
            return responseSuccess('لیست مخاطبان برای شما ایمیل شد', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createContact($email, $listIds)
    {
        $createContact = new CreateContact();
        $createContact['email'] = $email;
        $createContact['listIds'] = $listIds;
        try {
            $result = $this->apiContacts->createContact($createContact);
            return responseSuccess('مخاطب اضافه شد', ['contactId' => $result->getId()]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createContacts($emails, $listId)
    {
        $contactIdentifiers = new \SendinBlue\Client\Model\AddContactToList();
        $contactIdentifiers['emails'] = array_values($emails);
        try {
            $result = $this->apiContacts->addContactToList($listId, $contactIdentifiers);
            return responseSuccess('', $result);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function deleteContact($identifier)
    {
        try {
            $this->apiContacts->deleteContact($identifier);
            return responseSuccess('ایمیل با موفقیت حذف شد', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function deleteContacts($listId, $emails)
    {
        $contactIdentifiers = new \SendinBlue\Client\Model\RemoveContactFromList();
        $contactIdentifiers['emails'] = $emails;
        try {
            $result = $this->apiContacts->removeContactFromList($listId, $contactIdentifiers);
            return responseSuccess('حذف با موفقیت انجام شد', $result->getContacts()->getSuccess());
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function updateContact($identifier, $newEmail, $firstName)
    {
        $updateContact = new UpdateContact();
        $updateContact['attributes'] = array('EMAIL' => $newEmail, 'FIRSTNAME' => $firstName);
        try {
            $this->apiContacts->updateContact($identifier, $updateContact);
            return responseSuccess('ایمیل با موفقیت ویرایش شد', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function contactCampaignsStatistics($identifier, $startDate, $endDate)
    {
        try {
            $result = $this->apiContacts->getContactStats($identifier, $startDate, $endDate);
            $result_data[] = array(
                'messagesSent' => $result->getmessagesSent(),
                'hardBounces' => $result->getHardBounces(),
                'softBounces' => $result->getSoftBounces(),
                'complaints' => $result->getComplaints(),
                'unsubscriptions' => $result->getUnsubscriptions(),
                'opened' => $result->getOpened(),
                'clicked' => $result->getClicked(),
                'transacAttributes' => $result->getTransacAttributes(),
                'delivered' => $result->getDelivered()
            );
            return responseSuccess('', $result_data);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function getAllList($limit, $offset, $sort)
    {
        try {
            $result = $this->apiContacts->getLists($limit, $offset, $sort);
            $result_data[] = array(
                'count' => $result->getCount(),
                'list' => $result->getLists()
            );
            return responseSuccess('', $result_data);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createList($name, $folderId)
    {
        $createList = new \SendinBlue\Client\Model\CreateList();
        $createList['name'] = $name;
        $createList['folderId'] = $folderId;
        try {
            $result = $this->apiContacts->createList($createList);
            return responseSuccess('لیست ایجاد شد', ['listId' => $result->getId()]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function updateList($name, $listId)
    {
        $updateList = new \SendinBlue\Client\Model\UpdateList();
        $updateList['name'] = $name;
        try {
            $this->apiContacts->updateList($listId, $updateList);
            return responseSuccess('', '');
        } catch (\Exception $e) {
            return responseServerError($e->getMessage());
        }
    }

    public function deleteList($listId)
    {
        try {
            $this->apiContacts->deleteList($listId);
            return responseSuccess('', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function getContactsFromList($listId, $modifiedSince, $limit, $offset)
    {
        try {
            $result = $this->apiContacts->getContactsFromList($listId, $modifiedSince, $limit, $offset);
            $result_data[] = array(
                'count' => $result->getCount(),
                'contacts' => $result->getContacts()
            );
            return responseSuccess($result_data, '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function getFolders($limit, $offset)
    {
        try {
            $result = $this->apiContacts->getFolders($limit, $offset);
            $result_data[] = array(
                'count' => $result->getCount(),
                'folders' => $result->getFolders()
            );
            return responseSuccess($result_data, '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function deleteFolder($folderId)
    {
        try {
            $this->apiContacts->deleteFolder($folderId);
            return responseSuccess('', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createFolder($name)
    {
        $createFolder = new \SendinBlue\Client\Model\CreateUpdateFolder();
        $createFolder['name'] = $name;
        try {
            $result = $this->apiContacts->createFolder($createFolder);
            return responseSuccess('فولدر ایجاد شد', ['folderId' => $result->getId()]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createCampaign($tag
        ,                          $sender_name
        ,                          $name
        ,                          $templateId
        ,                          $subject
        ,                          $listIds
        ,                          $exclusionListIds
        ,                          $type
        ,                          $header
        ,                          $footer
        ,                          $utmCampaign
        ,                          $params_parameter
        ,                          $params_address
        ,                          $params_subject)
    {
        $emailCampaigns = new \SendinBlue\Client\Model\CreateEmailCampaign();
        $emailCampaigns['tag'] = $tag;
        $emailCampaigns['sender'] = array('name' => $sender_name, 'email' => config('services.sendinBlue.sender'));
        $emailCampaigns['name'] = $name;
        $smtpTemplate['attachmentUrl'] = null;
        $emailCampaigns['templateId'] = $templateId;
        $emailCampaigns['subject'] = $subject;
        $emailCampaign['recipients'] = array(
            'listIds' => $listIds, 'exclusionListIds' => $exclusionListIds
        );
        $emailCampaigns['inlineImageActivation'] = false;
        $emailCampaigns['mirrorActive'] = false;
        $emailCampaigns['recurring'] = false;
        $emailCampaigns['type'] = 'classic';
        $emailCampaigns['header'] = $header;
        $emailCampaigns['footer'] = $footer;
        $emailCampaigns['utmCampaign'] = $utmCampaign;
        $emailCampaigns['params'] = array('PARAMETER' => $params_parameter, 'ADDRESS' => $params_address, 'SUBJECT' => $params_subject);
        try {
            $result = $this->apiEmailCampaigns->createEmailCampaign($emailCampaigns);
            return responseSuccess('کمپین با موفقیت ایجاد شد', ['campaignId' => $result->getId()]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function updateCampaign($campaignId
        ,                          $tag
        ,                          $sender_name
        ,                          $name
        ,                          $templateId
        ,                          $subject
        ,                          $listIds
        ,                          $exclusionListIds
        ,                          $type
        ,                          $header
        ,                          $footer
        ,                          $utmCampaign
        ,                          $params_parameter
        ,                          $params_address
        ,                          $params_subject)
    {
        $campaignId = $campaignId;
        $emailCampaign = new UpdateEmailCampaign();
        $emailCampaign['tag'] = $tag;
        $emailCampaign['sender'] = array('name' => $sender_name, 'email' => config('services.sendinBlue.sender'));
        $emailCampaign['name'] = $name;
        $smtpTemplate['attachmentUrl'] = null;
        $emailCampaign['templateId'] = $templateId;
        $emailCampaign['subject'] = $subject;
        $emailCampaign['recipients'] = array(
            'listIds' => $listIds, 'exclusionListIds' => $exclusionListIds
        );
        $emailCampaign['inlineImageActivation'] = false;
        $emailCampaign['mirrorActive'] = false;
        $emailCampaign['recurring'] = false;
        $emailCampaign['type'] = 'classic';
        $emailCampaign['header'] = $header;
        $emailCampaign['footer'] = $footer;
        $emailCampaign['utmCampaign'] = $utmCampaign;
        $emailCampaign['params'] = array('PARAMETER' => $params_parameter, 'ADDRESS' => $params_address, 'SUBJECT' => $params_subject);
        try {
            $this->apiEmailCampaigns->updateEmailCampaign($campaignId, $emailCampaign);
            return responseSuccess('کمپین با موفقیت بروز شد', '');
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }

    public function createTemplate($sender_name,$templateName, $text, $subject)
    {
        //htmlUrl,attachmentUrl  swaggerTypes:string
        $smtpTemplate = new \SendinBlue\Client\Model\CreateSmtpTemplate();
        $smtpTemplate['sender'] = array('name' => $sender_name, 'email' => config('services.sendinBlue.sender'));
        $smtpTemplate['templateName'] = $templateName;
        $smtpTemplate['htmlContent'] = '<html><body><h1>' . $text . '</h1></body></html>';
        $smtpTemplate['subject'] = $subject;
        $smtpTemplate['isActive'] = true;
        $smtpTemplate['attachmentUrl'] = null;
        try {
            $result = $this->apiTransactional->createSmtpTemplate($smtpTemplate);
            return responseSuccess('قالب با موفقیت ایجاد شد', ['TemplateId' => $result->getId()]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), '');
        }
    }
}
