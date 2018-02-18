<?php

namespace DUCS\Sankalan\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Template;
use \DUCS\Sankalan\Database;

/**
 *  Allow user to login either via Google account
 *      (if not register, then register)
 *  or, via use of a user id and a password assigned during registeration
 */
class LoginUser
{
    private $req;
    private $res;
    private $session;
    private $client;
    private $auth_service;
    public $user;
    public $error;
    public function __construct()
    {
        $this->req = Request::createFromGlobals();
        $this->session = new Session();
        $this->session->start();
        $this->client = new \Google_Client();
        $this->client->setAuthConfig(getcwd() . '/../src/app/Sankalan/Auth/client_credentials.json');
        $this->client->setScopes([
            "https://www.googleapis.com/auth/plus.me",
            "https://www.googleapis.com/auth/userinfo.profile",
            "https://www.googleapis.com/auth/userinfo.email",
        ]);
        $this->res = new Response();
    }

    public function login($next = '/sankalan')
    {
        $uid_session = $this->session->get('ugid');
        if (!isset($uid_session)) {
            $uid_session = $this->session->get('uid');
        }
        if (isset($uid_session)) {
            return new RedirectResponse($next);
        }
        $email = $this->req->request->get('email');
        $pass = $this->req->request->get('password');

        include getcwd() . '/../src/app/utils/database.php';
        $id = formatEmail($email);

        $db = new Database();
        $user = $db->query('SELECT * FROM user WHERE email = :email LIMIT 1', ['email' => $id], true);

        if (!$user) {
            return new RedirectResponse('/sankalan/login?invalid=1');
        }

        // check password
        include getcwd() .'/../src/app/utils/database.php';
        $secret = userHashToSecret($user['hash']);
        if ($pass === $secret) {
            $this->session->set('uid', $user['id']);
            $this->session->set('ugid', $user['gid']);
            return new RedirectResponse($next);
        } else {
            // invalid password
            return new RedirectResponse('/sankalan/login?invalid=2');
        }
    }

    public function view() {
        if (isset($user)) {
            $this->session->set('ugid', $user['gid']);
        } else {
            $uid_session = $this->session->get('ugid');
            if (!isset($uid_session)) {
                $uid_session = $this->session->get('uid');
            }
        }
        if (isset($uid_session)) {
            return new RedirectResponse('/sankalan');
        }
        $invalid = $this->req->query->get('invalid');
        $html = Template::render('sn18/login', [
            'login_url' => $this->client->createAuthUrl(),
            'invalid' => $invalid,
            'state' => [
                'type' => isset($invalid) ? 'error' : '',
                'msg' => isset($invalid)
                    ? 'Login failed. ' . (($invalid == 1) ? 'Invalid Email.' : 'Invalid Credentials.')
                    : '',
            ]
        ]);
        $this->res = new Response($html);
        return $this->res;
    }
}
