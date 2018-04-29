<?php

namespace DUCS\Sankalan\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DUCS\Template;
use \DUCS\Sankalan\Database;

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
        $this->client->setAuthConfig(getcwd() . '/../src/app/Sankalan/Auth/client_credentials.json');
        $this->client->setScopes([
            "https://www.googleapis.com/auth/plus.me",
            "https://www.googleapis.com/auth/userinfo.profile",
            "https://www.googleapis.com/auth/userinfo.email",
        ]);
    }

    private function getUserData()
    {
        $user = array(
            'name' => $this->user['name'],
            'gid' => $this->user['gid'],
            'email' => $this->user['email'],
        );

        if (empty($user['name'])) {
            $user['name'] = trim($this->req->request->get('name'));
        }

        if (empty($user['email'])) {
            $user['email'] = trim($this->req->request->get('email'));
        }

        $user['mobile'] = trim($this->req->request->get('mobile'));
        $user['org'] = trim($this->req->request->get('org'));

        $user['accomodation'] = trim($this->req->request->get('accmo'));
        if (isset($user['accomodation']) && $user['accomodation'] === 'on') {
            $user['accomodation'] = '1';
        } else {
            $user['accomodation'] = '0';
        }

        return $user;
    }

    public function registerUser()
    {
        $ugid = $this->session->get('ugid');
        if (!isset($this->user) && isset($ugid)) { // already logged in
            $this->res = new RedirectResponse('/sankalan/');
            return $this->res;
        }
        $db = new Database();
        $userAlreadyRegistered = $db->query('SELECT id, gid FROM user WHERE gid = :gid', array('gid' => $this->user['gid']), true);

        if ($userAlreadyRegistered) {
            $this->session->remove('user');
            $this->session->set('ugid', $userAlreadyRegistered['gid']);
            $this->session->set('uid', $userAlreadyRegistered['id']);
            $this->res = new RedirectResponse('/sankalan');
            return $this->res;
        }

        $user = $this->getUserData();

        // validata user data
        foreach ($user as $attr => $value) {
            if (!isset($user[$attr]) || (empty($value) && $value !== '0')) {
                $this->res->headers->set('Bad-Field', $attr);
                $this->res->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
                return $this->res;
            }
        }

        // validate user's mobile number
        if (!preg_match('/^[0-9]{10}+$/', $user['mobile'])) {
            $this->res->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $this->res;
        }

        // customize data further for db update
        include getcwd() . '/../src/app/utils/database.php';
        $user['now'] = time();
        $user['email'] = formatEmail($user['email']);
        $user['hash'] = md5(serialize($user));

        $count = $db->modify('INSERT INTO `user` (
            `ts`, `gid`, `name`, `email`, `mobile`, `org`, `accomodation`, `hash`
        ) VALUES (
            :now, :gid, :name, :email, :mobile, :org, :accomodation, :hash
        )', $user);

        echo "<pre>";
        var_dump($user);
        var_dump($count);
        if ($count === 1) {
            $this->session->remove('user');
            $this->session->set('ugid', $user['gid']);
            $this->res = new RedirectResponse('/sankalan');
        } else {
            $this->res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->res;
    }

    public function view()
    {
        $this->user = $this->session->get('user');

        if (isset($this->user)) {
            $gid = $this->user['gid'];
        }

        $uid = $this->session->get('uid');
        $ugid = $this->session->get('ugid');

        if (isset($uid) || isset($ugid)) {
            $this->res = new RedirectResponse('/sankalan');
            $this->res->headers->set('Skln', 'uid set in session');
            $this->user = $this->session->remove('user');
            return $this->res;
        }

        if (isset($gid)) {
            $db = new Database();
            $userAlreadyRegistered = $db->query('SELECT id, gid FROM user WHERE gid = :gid LIMIT 1', array('gid' => $gid), true);
            if ($userAlreadyRegistered) {
                $this->res = new RedirectResponse('/sankalan');
                $this->res->headers->set('Skln', '$userAlreadyRegistered');
                $this->user = $this->session->remove('user');
                $this->session->set('uid', $userAlreadyRegistered['id']);
                $this->session->set('ugid', $userAlreadyRegistered['gid']);
                return $this->res;
            }
        }

        $html = Template::render('sn18/register', array(
            'login_url' => $this->client->createAuthUrl(),
            'user' => $this->user,
            'error' => $this->error,
        ));
        $this->res->setContent($html);
        return $this->res;
    }
}
