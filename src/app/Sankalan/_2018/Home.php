<?php
namespace DUCS\Sankalan\_2018;

use DUCS\Template;
use DUCS\Sankalan\_2018\Database\Database;

class Home
{
    public static function view()
    {
        $db = new Database();
        $html = '';
        for ($i=1; $i < 7; $i++) {
            $item = $db->query('SELECT * FROM test WHERE id = :id LIMIT 1', array(':id' => $i), true);
            $html .= $item['id'] . ' - ' . $item['name'] . '<br/>';
        }
        // $html = Template::render('layout', array(
        //     'title' => 'Welcome to Sankalan',
        //     'content' => 'We welcome you to Sankalan 2018',
        // ));
        return $html;
    }
}
