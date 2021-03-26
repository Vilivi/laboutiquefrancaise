<?php 

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail {

    private $api_key = '32d249329343d8f159f1977f389d71ef';
    private $api_key_secret = '80edcd6b592621857ec7f72872425f47';

    public function send($to_email, $to_name, $subject, $content){
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "vilivi.b@gmail.com",
                        'Name' => "Test"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2702092,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}