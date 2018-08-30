<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 26/02/2018
 * Time: 14:57
 */

namespace App\Controller\Admin;

use App\Controller\CoreController;
use App\Entity\Offer;
use App\Form\OfferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OfferController
 * @package App\Controller\Admin
 * @Route("/admin/offers")
 */
class OfferController extends CoreController
{
    /**
     * @Route("/offers.json", name="offers_list")
     * @Method("POST")
     */
    public function getListOffers()
    {
        $offers = $this->getEm()->getRepository(Offer::class)->getAll();

        $results =
            [
                "sEcho" => 1,

                "iTotalRecords" => count($offers),

                "iTotalDisplayRecords" => count($offers),

                "aaData" => $offers
            ];

        $results = $this->get('jms_serializer')->serialize($results,'json');

        return new Response($results);
    }

    /**
     * @Route("/", name="offer_list")
     * @Method("GET")
     */
    public function getOffers()
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('admin/offers/index.html.twig');
    }

    /**
     * @Route("/{id}/show", name="offer_show")
     * @Method("GET")
     */
    public function getOffer(Request $request, $id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $offer = $this->getEm()->getRepository(Offer::class)->getOne($id);
        if(!$offer instanceof Offer)
        {
            throw $this->createNotFoundException("Offre introuvable");
        }
        return $this->render('admin/offers/show.html.twig', ['offer'=>$offer]);
    }

    /**
     * @Route("/create", name="offer_create")
     */
    public function createOffer(Request $request)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer, ['method'=>'POST','action'=>$this->generateUrl('offer_create')]);
        $form->add('submit', SubmitType::class, ['label'=>'Enregistrer']);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $start = new \DateTime($offer->getStartDate().' '.$offer->getStartTime());
            $end = new \DateTime($offer->getEndDate().' '.$offer->getEndTime());
            if($start >= $end)
            {
                $this->addFlash('danger','<strong>Ooops</strong>, la date d\'expiration doit être supérieure à la date de début');
            }
            else
            {
                $offer->setStartDate(new \DateTime($offer->getStartDate()));
                $offer->setStartTime(new \DateTime($offer->getStartTime()));
                $offer->setEndDate(new \DateTime($offer->getEndDate()));
                $offer->setEndTime(new \DateTime($offer->getEndTime()));
                $this->getEm()->persist($offer);
                $this->getEm()->flush();
                $this->addFlash('success', '<strong>Félicitations</strong>, offre créé avec succès');
                return $this->redirectToRoute('offer_show',array('id'=>$offer->getId()));
            }
        }
        return $this->render('admin/offers/new.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @Route("/{id}/update", name="offer_update")
     */
    public function updateOffer(Request $request,$id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $offer = $this->getEm()->getRepository(Offer::class)->getOne($id);
        if(!$offer instanceof Offer)
        {
            throw $this->createNotFoundException("Offre introuvable");
        }
        $offer->setStartDate($offer->getStartDate()->format('Y-m-d'));
        $offer->setStartTime($offer->getStartTime()->format('H:i'));
        $offer->setEndDate($offer->getEndDate()->format('Y-m-d'));
        $offer->setEndTime($offer->getEndTime()->format('H:i'));
        $form = $this->createForm(OfferType::class, $offer, ['method'=>'POST','action'=>$this->generateUrl('offer_update',['id'=>$id])]);
        $form->add('submit', SubmitType::class, ['label'=>'Mettre à jour']);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $start = new \DateTime($offer->getStartDate().' '.$offer->getStartTime());
            $end = new \DateTime($offer->getEndDate().' '.$offer->getEndTime());
            if($start >= $end)
            {
                $this->addFlash('danger','<strong>Ooops</strong>, la date d\'expiration doit être supérieure à la date de début');
            }
            else
            {
                $offer->setStartDate(new \DateTime($offer->getStartDate()));
                $offer->setStartTime(new \DateTime($offer->getStartTime()));
                $offer->setEndDate(new \DateTime($offer->getEndDate()));
                $offer->setEndTime(new \DateTime($offer->getEndTime()));
                $this->getEm()->flush();
                $this->addFlash('success', '<strong>Félicitations</strong>, offre mise à jour avec succès');
                return $this->redirectToRoute('offer_show',['id'=>$id]);
            }
        }
        return $this->render('admin/offers/edit.html.twig', ['form'=>$form->createView(), 'offer'=>$offer]);
    }

    /**
     * @Route("/{id}/delete", name="offer_delete")
     * @Method("POST")
     */
    public function deleteOffer(Request $request, $id)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin_login');
        }
        $offer = $this->getEm()->getRepository(Offer::class)->getOne($id);
        if(!$offer instanceof Offer)
        {
            throw $this->createNotFoundException("Offre introuvable");
        }
        $this->getEm()->remove($offer);
        $this->getEm()->flush();
        $this->addFlash('success', '<strong>Félicitations</strong>, offre supprimé avec succès');
        return $this->redirectToRoute('offer_list');
    }


}