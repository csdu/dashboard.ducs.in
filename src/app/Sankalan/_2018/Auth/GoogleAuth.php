<?php

namespace DUCS\Sankalan\_2018\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DUCS\Template;
use \DUCS\Sankalan\_2018\Database\Database;

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
    public $user;
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
        $this->client->setAuthConfig(getcwd() . '/../src/app/Sankalan/_2018/Auth/client_credentials.json');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $this->res = new Response();
    }

    public function authorize()
    {
        $code = $this->req->query->get('code');
        $error = $this->req->query->get('error');
        if (isset($code)) {
            try {
                $this->client->authenticate($code);
                $google_oauth =new \Google_Service_Oauth2($this->client);
                $this->user = $google_oauth->userinfo->get();
                $this->session->set('user', $this->user);
                $this->res = new RedirectResponse('/sankalan/register');
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
