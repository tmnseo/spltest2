<?php


use Tygh\Registry;

class Zoho
{

    private $Api;
    const accountingDepartmentID = '52311000000835173';
    const helpDeskDepartmentID = '52311000000007061';
    const providersDepartmentID = '52311000000351045';
    const ordersDepartmentID = '52311000001088045';

    public function __construct()
    {
        $this->Api = new zohodeskAPI(self::getActualAccessToken(), Registry::get('config.zoho_api_orgID'));
    }

    public function createContact($name, $email, $phone = null)
    {
        $contactData = [
            'lastName' => $name,
            'email' => $email,
        ];
        if ($phone)
            $contactData['mobile'] = $phone;
        try {
            return $this->Api->createContact($contactData);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function newProvider($contactID, $description, $agentPhone = null, $agentEmail = null)
    {
        return $this->createTicket(
            'Новый поставщик',
            Zoho::providersDepartmentID,
            $contactID,
            $description,
            $agentPhone,
            $agentEmail
        );
    }

    public function newPayment($contactID, $description, $orderID)
    {
        return $this->createTicket(
            "Платёж ${$orderID} требует внимания",
            Zoho::accountingDepartmentID,
            $contactID,
            $description
        );
    }

    public function newQuestion($contactID, $description, $topic = null, $agentPhone = null, $agentEmail = null, $uploads = [])
    {
        return $this->createTicket(
            $topic ? $topic : 'Вопрос покупателя',
            Zoho::helpDeskDepartmentID,
            $contactID,
            $description,
            $agentPhone,
            $agentEmail,
            $uploads
        );
    }

    public function newNotification($contactID, $description, $topic)
    {
        return $this->createTicket(
            $topic,
            Zoho::ordersDepartmentID,
            $contactID,
            $description
        );
    }

    public function newReclamation($contactID, $description, $agentPhone = null, $agentEmail = null)
    {
        return $this->createTicket(
            'Новая рекламация',
            Zoho::helpDeskDepartmentID,
            $contactID,
            $description,
            $agentPhone,
            $agentEmail
        );
    }

    private function createTicket($subject, $departmentId, $contactID, $description = null,
                                  $agentPhone = null, $agentEmail = null, $uploads = [])
    {
        $ticketData = [
            'subject' => $subject,
            'departmentId' => $departmentId,
            'contactId' => $contactID,
        ];
        if ($description)
            $ticketData['description'] = $description;
        if ($agentPhone)
            $ticketData['phone'] = $agentPhone;
        if ($agentEmail)
            $ticketData['email'] = $agentEmail;
        if ($uploads)
            $ticketData['uploads'] = $uploads;

        try {
            $ticket = $this->Api->createTicket($ticketData);
            return isset($ticket->id);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function uploadFile($pathToFile)
    {
        $uploaded = $this->Api->uploadFile($pathToFile);
        if ($uploaded->id)
            return $uploaded->id;
        return false;
    }

    static function getActualAccessToken()
    {
        $timeToExpire = ZohoAuthSettings::getExpireTime() - time();

        if (($timeToExpire < 30) or !$timeToExpire) {
            (new ZohoOAuth())->refreshToken();
        }
        return ZohoAuthSettings::getAccessToken();
    }
}