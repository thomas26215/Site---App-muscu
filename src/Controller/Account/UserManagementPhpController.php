<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Model\BDD\UserBase;
use Exception;

class UserManagementPhpController extends AbstractController
{
    private UserBase $base;

    public function __construct(UserBase $base)
    {
        $this->base = $base;
    }

    #[Route('/auth/management/{page}/', name: 'managementAuth')]
    public function index(Request $request, string $page): Response
    {
        if ($request->isMethod('POST')) {
            return $this->handlePostRequest($request);
        }

        return $this->handlePageRequest($page, $request);
    }

    private function handlePostRequest(Request $request): Response
    {
        if ($request->request->get("submit") === 'forgetPassword') {
            return $this->handleForgetPasswordRequest($request);
        }

        if ($request->request->get("submit") === "verifyAccount") {
            return $this->handleVerifyAccountRequest($request);
        }

        if($request->request->get("submit") === "newPassword"){
            return $this->handleNewPasswordRequest($request);
        }

        return $this->redirectToRoute('managementAuth', ['page' => 'default']); // Gérer un cas par défaut
    }

    private function handleForgetPasswordRequest(Request $request): Response
    {
        $email = $request->request->get('email');

        if (empty($email)) {
            return $this->addFlashAndRedirect('error', 'Veuillez entrer une adresse e-mail.', 'forget_password');
        }

        if ($this->base->isEmailVerified($email)) {
            $this->base->askNewPassword($email);
            return $this->redirectToRoute('managementAuth', ['page' => 'new_password']);
        } else {
            return $this->addFlashAndRedirect('error', 'Email non vérifié.', 'forget_password');
        }
    }

    private function handleVerifyAccountRequest(Request $request): Response
    {
        try {
            $code = $request->request->get('code');
            $email = $request->request->get('email');

            if ($this->base->verifyAccountWithCode($code, $email)) {
                $this->base->confirmVerificationCode($email);
                return $this->redirectToRoute("auth");
            } else {
                throw new Exception("Email ou code de vérification invalide. Réessayez !");
            }
        } catch (Exception $e) {
            return $this->addFlashAndRedirect('error', $e->getMessage(), 'verify_account');
        }
    }

    private function handleNewPasswordRequest(Request $request): Response{
        try{
            $email = $request-> request->get('email');
            $code = $request->request->get('code');
            $password = $request->request->get('password');


            if($this->base->insertNewPassword($email, $code, $password)){
                return $this->redirectToRoute("auth");
            }else{
                throw new Exception("Email ou code invalide. Réessayer !");
            }
        } catch(Exception $e){
            return $this->addFlashAndRedirect('error', $e->getMessage(), 'new_password');
        }
    }


    private function handlePageRequest(string $page, Request $request): Response
    {
        switch ($page) {
            case "forget_password":
                return $this->forgetPassword($request);
            case "new_password":
                return $this->newPassword($request);
            case "verify_account":
                return $this->verifyAccount($request);
            default:
                // Gérer le cas où la page n'existe pas
                return $this->redirectToRoute("auth"); // Redirection vers une autre route
        }
    }

    private function addFlashAndRedirect(string $type, string $message, string $page): Response
    {
        $this->addFlash($type, $message);
        return $this->redirectToRoute('managementAuth', ['page' => $page]);
    }

    public function forgetPassword(Request $request): Response
    {
        return $this->render('user_management/forget_password.html.twig', [
            'controller_name' => self::class,
        ]);
    }

    public function verifyAccount(Request $request): Response
    {
        return $this->render('user_management/verify_account.html.twig', [
            'controller_name' => self::class,
        ]);
    }
    public function newPassword(Request $request):Response{
        return $this->render('user_management/new_password.html.twig', [
            'controller_name' => self::class,
        ]);
    }

}
