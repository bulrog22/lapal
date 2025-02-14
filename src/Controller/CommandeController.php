<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $request = Request::createFromGlobals();
        $cookie = $request->cookies->get('commande');
        
        if (isset($cookie)) {
            $contentCookie = json_decode($cookie);

            $commandeId = $contentCookie->command_id;
            $nom = $contentCookie->nom;
            $prenom = $contentCookie->prenom;

        }
     
        if (isset($commandeId)){

            $response = $this->render('commande/index.html.twig', [
                'commande' => $commandeRepository->findOneById($commandeId),
                ]);
            return $response;
        }
        else {
            return $this->redirectToRoute('passe_commande_index');
        }
    }

    /**
     * @Route("/new", name="commande_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $commande = new Commande();

        $request = Request::createFromGlobals();
        $cookie = $request->cookies->get('commande');

        $form = $this->createForm(CommandeType::class, $commande, [
            'lastNom' => $cookie->nom,
            'lastPrenom' => $cookie->prenom,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();

            $cookieValue = [
                'command_id' => $commande->getId(),
                'nom' => $commande->getNom(),
                'prenom' => $commande->getPrenom(),
            ];

            $coockie = new Cookie('commande', json_encode($cookieValue), time() + ( 2 * 365 * 24 * 60 * 60));
            $response = $this->redirectToRoute('commande_index');
            $response->headers->setCookie($coockie);

            return $response;
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Commande $commande): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Commande $commande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $poidCommande = 0;
            foreach ($commande->getLigneCommandes() as $ligneCommande ){
                $poidCommande += $ligneCommande->getPain()->getPoid() * $ligneCommande->getQuantite();
            }
            
            $poidRestant = $commande->getJourDistrib()->getPoidRestant();
            $poidRestant -= $poidCommande;

            $poidRestant = $commande->getJourDistrib()->setPoidRestant($poidRestant);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commande);
            $entityManager->flush();

        }
        $response = $this->redirectToRoute('passe_commande_index');
        $response->headers->clearCookie('commande');
        return $response;
    }
}
