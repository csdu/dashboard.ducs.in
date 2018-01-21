<?php

namespace DUCS\Sankalan\_2018\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DUCS\Template;
use \DUCS\Sankalan\_2018\Database;

/**
 *
 */
class RegisterUser
{

    private $req;
    private $res;
    private $session;
    private $client;
    private $error;
    private $user;
    function __construct()
    {
        $this->req = Request::createFromGlobals();
        $this->session = new Session();
        $this->res = new Response();
        $this->user = $this->session->get('user');
        $this->client = new \Google_Client();
        $this->client->setAuthConfig(getcwd() . '/../src/app/Sankalan/_2018/Auth/client_credentials.json');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
    }

    public function registerUser()
    {
        if (!isset($this->user)) {
            $this->res = new RedirectResponse('/sankalan/me');
            return $this->res;
        }
        $db = new Database();
        $userAlreadyRegistered = $db->query('SELECT id FROM user WHERE id = :id', array('id' => $this->user['id']), true);
        if ($userAlreadyRegistered) {
            $this->session->remove('user');
            $this->res = new RedirectResponse('/sankalan/me');
            return $this->res;
        }
        $user = array(
            'name' => $this->user['name'],
            'id' => $this->user['id'],
            'email' => $this->user['email'],
        );

        if (empty($user['name'])) {
            $user['name'] = trim($this->req->request->get('name'));
        }

        $user['mobile'] = trim($this->req->request->get('mobile'));
        $user['org'] = trim($this->req->request->get('org'));

        foreach ($user as $attr => $value) {
            if (!isset($user[$attr]) || empty($value)) {
                $this->res->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
                return $this->res;
            }
        }

        // validate mobile number
        if (!preg_match('/^[0-9]{10}+$/', $user['mobile'])) {
            $this->res->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $this->res;
        }

        $user['now'] = time();
        $user['mobile'] = substr($user['mobile'], 0, 10);
        $user['email'] = explode('@gmail.com', $user['email'])[0];
        $user['hash'] = md5(serialize($user));

        $count = $db->modify('INSERT INTO user (
            ts, id, name, email, mobile, org, hash
        ) VALUES (
            :now, :id, :name, :email, :mobile, :org, :hash
        )', $user);
        if (!$count) {
            $this->res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $this->session->remove('user');
            $this->res = new RedirectResponse('/sankalan/ticket');
        }
        return $this->res;
    }

    public function view()
    {
        $this->user = $this->session->get('user');
        if (!isset($this->user)) {
            $uid = $this->session->get('uid');
        } else {
            $uid = $this->user['id'];
            $this->session->set('uid', $uid);
        }

        $db = new Database();
        $userAlreadyRegistered = $db->query('SELECT id FROM user WHERE id = :id LIMIT 1', array('id' => $uid), true);
        if ($userAlreadyRegistered) {
            $this->res = new RedirectResponse('/sankalan/me');
            $this->user = $this->session->remove('user');
            return $this->res;
        }

        $html = Template::render('s18/register', array(
            'login_url' => $this->client->createAuthUrl(),
            'user' => $this->user,
            'error' => $this->error,
        ));
        $this->res->setContent($html);
        return $this->res;
    }
}
