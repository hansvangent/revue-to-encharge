<?php
/**
 * This file will allow the syncing of new subscribers from Revue (https://www.getrevue.co/) to Encharge (https://encharge.io/)
 *
 * Upon running it should check the subscribers in Revue and synchronize (add or update them) into Encharge.
 *
 * If a user is already in Encharge it should update the user unless the user has unsubscribed in Encharge,
 * then the user should be unsubscribed in Revue as well.
 */


/* ---------------------------------------------------------------------------
 * Setup API keys and Communication Category ID's (Encharge) needed for syncing
 *
 * Revue API key can be found here: https://www.getrevue.co/app/integrations
 * Encharge API key can be found here: https://app.encharge.io/account/info
 * Encharge Communication Category ID's can be found here: https://app.encharge.io/settings/person-fields?personfields-folder-item=CommunicationCategories
 *
 * --------------------------------------------------------------------------- */

$revue_api_key = 'replace_me_with_your_revue_api_key';
$encharge_api_key = 'replace_me_with_your_encharge_api_key';
$encharge_marketing_emails_category_id = 'replace_me_with_your_marketing_email_category_id';
$encharge_transactional_emails_category_id = 'replace_me_with_your_transactional_email_category_id';

/* ---------------------------------------------------------------------------
 * Main class
 * --------------------------------------------------------------------------- */
class RevueToEncharge
{
    protected $revue_api_key;
    protected $encharge_api_key;

    public function __construct($revue_api_key, $encharge_api_key)
    {
        $this->revue_api_key = $revue_api_key;
        $this->encharge_api_key = $encharge_api_key;
    }

    public function getSubscriberRevue()
    {
        $curl = curl_init();
        $headers = array(
            "Authorization: Bearer {$this->revue_api_key}",// send token in Bearer header request
            "accept: application/json"
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL,"https://www.getrevue.co/api/v2/subscribers");

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => 'Test',
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $list_user_subscribers = curl_exec($curl);

        curl_close($curl);

        return $list_user_subscribers;
    }

    public function unsubscribesRevue($email)
    {
        $curl = curl_init();
        $fields = ([
            "email" => $email
        ]);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: Bearer {$this->revue_api_key}",// send token in Bearer header request
                "accept: application/json"
            )
        );

        curl_setopt($curl, CURLOPT_URL,"https://www.getrevue.co/api/v2/subscribers/unsubscribe");

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        $server_output = curl_exec($curl);

        curl_close($curl);

        return true;
    }

    public function getUserEncharge($userId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.encharge.io/v1/people?people[0][userId]=". $userId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', // for define content type that is json
                'X-Encharge-Token: '.$this->encharge_api_key.'', // send token in header request
            )
        );

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => 'Test',
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $user = curl_exec($ch);

        curl_close($ch);

        return $user;
    }

    public function addToEncharge($user)
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', // for define content type that is json
                'X-Encharge-Token: '.$this->encharge_api_key.'', // send token in header request
            )
        );

        $fields = json_encode([
            "name" => $user->first_name. ' ' . $user->last_name,
            "email" => $user->email ,
            "firstName" => $user->first_name,
            "lastName" => $user->last_name,
            "userId" => $user->id,
            'SOURCE' => 'Twitter Subscriber via Revue',
            'tags' => 'Twitter/Revue Subscriber',
            'CommunicationCategories.cat_'.$encharge_marketing_emails_category_id.'' => 'Opted in',
            'CommunicationCategories.cat_'.$encharge_transactional_emails_category_id.'' => 'Opted in'
        ]);

        curl_setopt($ch, CURLOPT_URL,"https://api.encharge.io/v1/people");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return true;
    }

    public function debug($message) {
        return file_put_contents('debug.log', "\n $message" , FILE_APPEND);
    }

    public function sync()
    {
        $list_sub = $this->getSubscriberRevue();
        $list_sub = json_decode($list_sub);

        if (count($list_sub) < 1) {
            $error = "syncing error: you don't have any subscriber on Revue";
            $this->debug($error);
        }

        $countAddToEncharge = 0;
        $countUnsubscribes = 0;
        $date = date("Y/m/d h:i:sa");

        foreach ($list_sub as $sub) {
            $user_encharge = ($this->getUserEncharge($sub->id));
            $user_encharge = json_decode($user_encharge);

            if (!$user_encharge->users) {
                $this->addToEncharge($sub);
                $countAddToEncharge += 1;
                $message = "$date Added $sub->email to Encharge from Revue";
                $this->debug($message);
                continue;
            }

            if ($user_encharge->users) {
                if ($sub->email != $user_encharge->users[0]->email || $sub->first_name != $user_encharge->users[0]->firstName || $sub->last_name!=$user_encharge->users[0]->lastName ) {
                    $this->addToEncharge($sub);
                    $countAddToEncharge += 1;
                    $message = "$date Updated $sub->email in Encharge with new Revue data";
                    $this->debug($message);
                }
            }

            if ($user_encharge->users[0]->unsubscribed == true) {
                $this->unsubscribesRevue($user_encharge->users[0]->email);
                $message = "$date Unsubscribed $sub->email from Revue";
                $this->debug($message);
                $countUnsubscribes += 1;
                continue;
            }
        }

        if ($countAddToEncharge == 0 && $countUnsubscribes == 0) {
            $message = "$date Nothing to update";
            $this->debug($message);
        }

        echo "Sync successfully!!\n";
    }
}

/* ---------------------------------------------------------------------------
 * Run the script
 * --------------------------------------------------------------------------- */

$app = new RevueToEncharge($revue_api_key, $encharge_api_key);
$app->sync();
?>
