<?php
namespace DUCS\Sankalan\Admin;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Template;
use DUCS\Sankalan\Database;

class Dashboard
{
    public static function view()
    {
        $session = new Session();
        $aid = $session->get('aid');
        $ap = $session->get('ap');
        $aname = $session->get('aname');

        $admin = [
            'id' => $aid,
            'privilage' => $ap,
            'name' => $aname,
        ];

        if (isset($aid)) {
            $html = Template::render('sn18/admin/dashboard', $admin);
            return $html;
        }

        return new RedirectResponse('/admin/login');
    }
}
