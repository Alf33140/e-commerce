<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')] #cette annotation définit une route pour la page de connexion de l'application. La route est accessible via l'URL '/login' et est nommée 'app_login'. Cela signifie que lorsque les utilisateurs accèdent à cette URL, la méthode login() de ce contrôleur sera exécutée pour gérer le processus de connexion.
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError(); #cette ligne utilise l'objet AuthenticationUtils pour récupérer la dernière erreur d'authentification, le cas échéant. Si une erreur s'est produite lors de la tentative de connexion précédente, elle sera stockée dans la variable $error. Cela permet à l'application d'afficher un message d'erreur approprié à l'utilisateur si la connexion a échoué.

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername(); #cette ligne utilise l'objet AuthenticationUtils pour récupérer le dernier nom d'utilisateur saisi par l'utilisateur lors de la tentative de connexion précédente. Si l'utilisateur a saisi un nom d'utilisateur, il sera stocké dans la variable $lastUsername. Cela permet à l'application de pré-remplir le champ du nom d'utilisateur dans le formulaire de connexion avec la dernière valeur saisie, ce qui peut améliorer l'expérience utilisateur en évitant de devoir ressaisir le nom d'utilisateur en cas d'erreur de connexion.

        return $this->render('security/login.html.twig', [ #cette ligne rend la vue 'security/login.html.twig' en passant les variables $lastUsername et $error à la vue. La vue peut ensuite utiliser ces variables pour afficher le dernier nom d'utilisateur saisi et tout message d'erreur d'authentification, offrant ainsi une expérience utilisateur plus conviviale lors de la connexion.
            'last_username' => $lastUsername, #on passe à la vue la variable $lastUsername pour qu'elle puisse être utilisée dans le template, par exemple pour pré-remplir le champ du nom d'utilisateur dans le formulaire de connexion.
            'error' => $error, #on passe à la vue la variable $error pour qu'elle puisse être utilisée dans le template, par exemple pour afficher un message d'erreur d'authentification si la connexion a échoué.
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')] #
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
