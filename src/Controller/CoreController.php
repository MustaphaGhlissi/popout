<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/02/2018
 * Time: 08:57
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}