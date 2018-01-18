<?php
namespace DUCS\Sankalan\_2018;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Sankalan\_2018\Database\Database;
use DUCS\Template;

class Ticket
{
    private $userid;
    private $req;
    public function __construct()
    {
        $this->req = Request::createFromGlobals();
        $session = new Session();
        $session->start();
        $this->userid = $session->get('uid');
    }

    private function findInDatabase()
    {
        // XXX:
        // $this->userid = '109704294546727240876';

        $db = new Database();
        return $db->query(
            "SELECT * FROM user WHERE id = :id",
            ['id' => $this->userid],
            true
        );
    }

    public function view()
    {
        $user = $this->findInDatabase();
        include getcwd() .'/../src/app/utils.php';
        if ($user) {
            $user['email'] = $user['email'] . '@gmail.com';
            $user['uid'] = $user['hash'];
            $user['secret'] = userHashToSecret($user['hash']);
            $user['secretMask'] = str_repeat('●', strlen($user['secret']));
            $html = Template::render('ticket', $user);
            $res = new Response();
            $res->setContent($html);
        } else {
            $res = new RedirectResponse('/sankalan/login');
        }
        return $res;
    }
}
