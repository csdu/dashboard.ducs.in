<?php
namespace DUCS\Sankalan;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Template;
use DUCS\Sankalan\Database;

/**
 * shows details about user account and acts as dashboard for the user
 */
class Me
{
    public static function view()
    {
        $session = new Session();
        $ugid = $session->get('ugid');
        $uid = $session->get('uid');

        if (isset($ugid)) {
            $db = new Database();
            $user = $db->query('SELECT * FROM user WHERE gid = :ugid LIMIT 1', ['ugid' => $ugid], true);
            if (!$user) {
                return new RedirectResponse('/sankalan/register');
            }
            $session->set('uid', $user['id']);
            $html = Template::render('sn18/index', $user);
            return $html;
        } elseif (isset($uid)) {
            $db = new Database();
            $user = $db->query('SELECT * FROM user WHERE id = :uid LIMIT 1', ['uid' => $uid], true);
            if (!$user) {
                return new RedirectResponse('/sankalan/register');
            }
            $session->set('ugid', $user['gid']);
            $html = Template::render('sn18/index', $user);
            return $html;
        }

        return new RedirectResponse('/sankalan/login');
    }
}
