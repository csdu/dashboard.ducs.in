<?php

namespace DUCS\Sankalan\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DUCS\Template;
use \DUCS\Sankalan\Database\Database;

/**
 * Handles google based authentication for a user
 *  by setting the session states
 *  on login in: session->set('user', user)
 *  on logout: session->invalidate()
 */
class GoogleAuth
{
    private $req;
    private $res;
    private $session;
    private $client;
    private $auth_service;
    public $user = [];
    public function __construct()
    {
        $this->req = Request::createFromGlobals();
        $this->session = new Session();
        $this->session->start();
        $http = new \GuzzleHttp\Client([
            'verify' => false // disable ssl
        ]);
        $this->client = new \Google_Client();
        $this->client->setHttpClient($http);
        $this->client->setAuthConfig(getcwd() . '/../src/app/Sankalan/Auth/client_credentials.json');
        $this->client->setScopes([
            "https://www.googleapis.com/auth/plus.me",
            "https://www.googleapis.com/auth/userinfo.profile",
            "https://www.googleapis.com/auth/userinfo.email",
        ]);
        $this->res = new Response();
    }

    private function getUserData($basic, $plus)
    {
        $this->user['gid'] = $basic['id'];

        // get user's gmail id
        $emails = $plus->getEmails();
        $isGmail = function ($email) {
            return strpos($email, '@gmail.com') !== false;
        };
        if (isset($emails)) {
            foreach ($emails as $email) {
                if ($email['type'] === 'account' && $isGmail($email['value'])) {
                    $this->user['email'] = $email['value'];
                    break;
                }
            }
        }
        if (!isset($this->user['email']) && $isGmail($basic['email'])) {
            $this->user['email'] = $basic['email'];
        }

        // get user's name
        $this->user['name'] = $basic['name'];
        if (!isset($this->user['name']) || empty($this->user['name'])) {
            $this->user['name'] = $plus->getDisplayName();
        }

        // get user's organisation
        $this->user['org'] = false;
        $orgs = $plus->getOrganizations();
        if (isset($orgs)) {
            foreach ($orgs as $org) {
                if ($org['primary'] === true) {
                    $this->user['org'] = $org['name'];
                    break;
                }
            }
        }
    }

    public function authorize()
    {
        $code = $this->req->query->get('code');
        $error = $this->req->query->get('error');
        if (isset($code)) {
            try {
                $this->client->authenticate($code);
                $peopleService = new \Google_Service_Plus($this->client);
                $auth_service = new \Google_Service_Oauth2($this->client);
                $plus = $peopleService->people->get('me');
                $basic = $auth_service->userinfo->get();
                $this->getUserData($basic, $plus);
                $this->session->set('user', $this->user);
                $this->res = new RedirectResponse('/sankalan/register', 301);
            } catch (Exception $e) {
                $this->res = new RedirectResponse('/sankalan/register?error=' . $e.getMessage());
            }
            return $this->res;
        } elseif (isset($error)) {
            return new RedirectResponse('/sankalan/register?error=' . $error);
        } else {
            return new RedirectResponse('/sankalan/account');
        }
    }

    public function logout()
    {
        $this->session->invalidate();
        $this->res = new RedirectResponse('/sankalan/login');
        return $this->res;
    }
}
