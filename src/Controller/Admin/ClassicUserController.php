<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/02/2018
 * Time: 08:55
 */

namespace App\Controller\Admin;

use App\Entity\User;
use App\Controller\CoreController;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class ClassicUserController
 * @package App\Controller\Admin
 * @Route("/admin/users/classic")
 */
class ClassicUserController extends CoreController
{
    /**
     * @Route("/classic-users.json", name="classic_users_list")
     * @Method("POST")
     */
    public function getListClassicUsers()
    {
        $classicUsers = $this->getEm()->getRepository(User::class)->getAllByRole('ROLE_PERSONAL');

        $results =
            [
            "sEcho" => 1,

            "iTotalRecords" => count($classicUsers),

            "iTotalDisplayRecords" => count($classicUsers),

            "aaData" => $classicUsers
        ];

        $results = $this->get('jms_serializer')->serialize($results,'json');

        return new Response($results);
    }

    /**
     * @Route("/", name="classic_users")
     * @Method("GET")
     */
    public function getClassicUsers()
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('admin/users/index.html.twig');
    }

    /**
     * @Route("/{id}/show", name="classic_user_show")
     * @Method("GET")
     */
    public function getClassicUser(Request $request, $id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PERSONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        return $this->render('admin/users/show.html.twig', ['user'=>$user]);
    }

    /**
     * @Route("/create", name="classic_user_create")
     */
    public function createClassicUser(Request $request)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['method'=>'POST','action'=>$this->generateUrl('classic_user_create')]);
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
                $user->setRoles(['ROLE_PERSONAL']);
                $user->setEnabled(true);
                $this->getEm()->persist($user);
                $this->getEm()->flush();
                $this->addFlash('success', '<strong>Félicitations</strong>, utilisateur créé avec succès');
                return $this->redirectToRoute('classic_user_show',array('id'=>$user->getId()));
            }
        }
        return $this->render('admin/users/new.html.twig', ['form'=>$form->createView()]);
    }


    /**
     * @Route("/{id}/update", name="classic_user_update")
     */
    public function updateClassicUser(Request $request, $id)
    {
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PERSONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        $form = $this->createForm(UserType::class, $user, ['method'=>'POST','action'=>$this->generateUrl('classic_user_update',['id'=>$id])]);
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
                return $this->redirectToRoute('classic_user_show',['id'=>$id]);
            }
        }
        return $this->render('admin/users/edit.html.twig', ['form'=>$form->createView()]);
    }



    /**
     * @Route("/{id}/delete", name="classic_user_delete")
     * @Method("POST")
     */
    public function deleteClassicUser($id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, "ROLE_PERSONAL");
        if(!$user instanceof User)
        {
            throw $this->createNotFoundException("Utilisateur introuvable");
        }
        $this->getEm()->remove($user);
        $this->getEm()->flush();

        $this->addFlash('success', '<strong>Félicitations</strong>, utilisateur supprimé avec succès');
        return $this->redirectToRoute('classic_users');
    }
}