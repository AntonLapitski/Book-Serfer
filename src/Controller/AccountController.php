<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index()
    {
        $user = $this->getUser();

        $account = $user->getAccount();

        //dd($account); die();
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'account' => $account,
        ]);
    }

    /**
     * @Route("/edit-account/{id}", name="account_edit")
     */
    public function edit($id)
    {
        return $this->render('account/edit.html.twig');
    }
}
