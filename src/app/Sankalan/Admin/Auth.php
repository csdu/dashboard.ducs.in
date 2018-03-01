<?php

namespace DUCS\Sankalan\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Template;
use \DUCS\Sankalan\Database;

class Auth
{
    private $req;
    private $res;
    private $session;
    public $user;
    public $error;
    public function __construct()
    {
        $this->req = Request::createFromGlobals();
        $this->session = new Session();
        $this->session->start();
        $this->res = new Response();
    }

    public function login($next = '/admin')
    {
        $admin_id = $this->session->get('aid');
        if (isset($admin_id)) {
            return new RedirectResponse($next);
        }
        $uname = $this->req->request->get('uname');
        $pass = $this->req->request->get('pass');

        include getcwd() . '/../src/app/utils/database.php';
        $enc_pass = encryptPassword($pass);

        $db = new Database();
        $admin = $db->query('SELECT * FROM admin WHERE uname = :uname LIMIT 1', ['uname' => $uname], true);

        if (!$admin) {
            // invalid admin id
            return new RedirectResponse('/admin/login?invalid=1');
        }

        // check password
        if ($admin['pass'] === $enc_pass) {
            $this->session->set('aid', $admin['id']);
            $this->session->set('ap', $admin['privilage']);
            $this->session->set('aname', $admin['uname']);
            return new RedirectResponse($next);
        } else {
            // invalid password
            return new RedirectResponse('/admin/login?invalid=2');
        }
    }

    public function logout() {
        $this->session->invalidate();
        return new RedirectResponse('/admin/login');
    }

    public function view() {
        $admin_id = $this->session->get('aid');
        if (isset($admin_id)) {
            $this->res = new RedirectResponse('/admin');
        } else {
            $invalid = $this->req->query->get('invalid');
            $state = [
                'type' => '',
                'msg' => '',
            ];
            if (isset($invalid)) {
                $state['type'] = 'error';
                $state['msg'] = 'Login Failed. ';
                $state['msg'] .= $invalid == 2
                    ? 'Invalid Credentials.'
                    : 'Invalid User.';
            }
            $html = Template::render('sn18/admin/login', [
                'invalid' => $invalid,
                'state' => $state,
            ]);
            $this->res = new Response($html);
        }
        return $this->res;
    }
}
