<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/02/2018
 * Time: 08:55
 */

namespace App\Controller\Admin;

use App\Controller\CoreController;
use App\Entity\User;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class ProfessionalController
 * @package App\Controller\Admin
 * @Route("/admin/users/pro")
 */
class ProfessionalController extends CoreController
{
    /**
     * @Route("/pro-users.json", name="pro_users_list")
     * @Method("POST")
     */
    public function getListProUsers()
    {
        $proUsers = $this->getEm()->getRepository(User::class)->getAllByRole('ROLE_PROFESSIONAL');

        $results =
            [
                "sEcho" => 1,

                "iTotalRecords" => count($proUsers),

                "iTotalDisplayRecords" => count($proUsers),

                "aaData" => $proUsers
            ];

        $results = $this->get('jms_serializer')->serialize($results,'json');

        return new Response($results);
    }

    /**
     * @Route("/", name="pro_users")
     * @Method("GET")
     */
    public function getProUsers()
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('admin/users/index.html.twig');
    }

    /**
     * @Route("/{id}/show", name="pro_user_show")
     * @Method("GET")
     */
    public function getProUser(Request $request, $id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PROFESSIONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        return $this->render('admin/users/show.html.twig', ['user'=>$user]);
    }

     /**
     * @Route("/create", name="pro_user_create")
     */
    public function createProUser(Request $request)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['method'=>'POST','action'=>$this->generateUrl('pro_user_create')]);
        $form->add('submit', SubmitType::class, ['label'=>'Enregistrer']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $exist = $this->getEm()->getRepository(User::class)->findOneBy(['email'=>$user->getEmail()]);
            if($exist instanceof User)
            {
                $this->addFlash('danger','<strong>Ooops</strong>, cet email est utilisé pour un autre compte.');
            }
            else
            {
                $encoder = $this->get('security.password_encoder');
                $plainPassword = $user->getPassword();
                $password = $encoder->encodePassword($user,$plainPassword);
                $user->setPassword($password);
                $user->setRoles(['ROLE_PROFESSIONAL']);
                $user->setEnabled(true);
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlash('success', '<strong>Félicitations</strong>, utilisateur créé avec succès');
                return $this->redirectToRoute('pro_user_show',array('id'=>$user->getId()));
            }
        }
        return $this->render('admin/users/new.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @Route("/{id}/update", name="pro_user_update")
     */
    public function updateProUser(Request $request,$id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PROFESSIONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        $form = $this->createForm(UserType::class, $user, ['method'=>'POST','action'=>$this->generateUrl('pro_user_update',['id'=>$id])]);
        $form->add('submit', SubmitType::class, ['label'=>'Mettre à jour']);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $exist = $this->getEm()->getRepository(User::class)->findOneBy(['email'=>$user->getEmail()]);
            if($exist instanceof User && $exist->getId() !== $user->getId())
            {
                $this->addFlash('danger','<strong>Ooops</strong>, cet email est utilisé pour un autre compte.');
            }
            else
            {
                $encoder = $this->get('security.password_encoder');
                $plainPassword = $user->getPassword();
                $password = $encoder->encodePassword($user,$plainPassword);
                $user->setPassword($password);
                $this->getEm()->flush();
                $this->addFlash('success', '<strong>Félicitations</strong>, utilisateur mis à jour avec succès');
                return $this->redirectToRoute('pro_user_show',['id'=>$id]);
            }
        }
        return $this->render('admin/users/edit.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @Route("/{id}/delete", name="pro_user_delete")
     * @Method("POST")
     */
    public function deleteProUser(Request $request, $id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PROFESSIONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        $this->getEm()->remove($user);
        $this->getEm()->flush();

        $this->addFlash('success', '<strong>Félicitations</strong>, utilisateur supprimé avec succès');
        return $this->redirectToRoute('pro_users');
    }

   
}