<?php
namespace DUCS\Sankalan\_2018;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Template;

/**
 *
 */
class Me
{
    public static function view()
    {
        $session = new Session();
        $uid = $session->get('uid');

        if (isset($uid)) {
            $db = new Database\Database();
            $user = $db->query('SELECT * FROM user WHERE id = :id LIMIT 1', ['id' => $uid], true);
            if (!$user) {
                return new RedirectResponse('/sankalan/register');
            }
            $html = Template::render('me', $user);
            return $html;
        }

        return new RedirectResponse('/sankalan/login');
    }
}
