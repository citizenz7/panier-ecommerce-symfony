<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(SessionInterface $session, ProductsRepository $productsRepository)
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrice() * $quantite;
        }

        return $this->render('cart/index.html.twig', compact("dataPanier", "total"));
    }

    /**
     * @Route("/cart/commande", name="cart_commande")
     */
    public function commande(SessionInterface $session, ProductsRepository $productsRepository)
    {
        //dd($session);
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrice() * $quantite;
        }

        return $this->render('cart/commande.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/send", name="cart_send")
     */
    public function sendCommande(SessionInterface $session, ProductsRepository $productsRepository, MailerInterface $mailer)
    {
        $panier = $session->get("panier", []);

        if($panier) {
            $dataPanier = [];
            $total = 0;
            
            foreach($panier as $id => $quantite){
                $product = $productsRepository->find($id);
                $dataPanier[] = [
                    "produit" => $product,
                    "quantite" => $quantite
                ];
                $total += $product->getPrice() * $quantite;
            }

            $email = (new TemplatedEmail())
                ->from('commande@maboutique.com')
                ->to('contact@maboutique.com')
                ->subject('Nouvelle commande depuis maboutique.com')
                ->htmlTemplate('cart/commande_email.html.twig')
                ->context([
                    'dataPanier' => $dataPanier,
                    'total' => $total
                ])
            ;

            $mailer->send($email);

            // Une fois la commande envoyée, on vide le panier : on vide la session.
            $session->clear();

            $this->addFlash('success', 'Votre commande a bien été envoyée ! Nous allons la traiter et la préparer dès que possible. Merci !');

            return $this->redirectToRoute('products_index');
        }
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function delete(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/cart/delete", name="cart_delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("cart_index");
    }

}
