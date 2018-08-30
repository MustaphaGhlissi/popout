<?php

namespace App\Controller\Admin;

use App\Controller\CoreController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Admin
 * @Route("/admin")
 */
class AdminController extends CoreController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="admin_dashboard")
     */
    public function dashboard()
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('admin/dashboard.html.twig');
    }
}