<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Article;
use App\Entity\User;

class ArticleVoter extends Voter
{

    // on choisit nous même quels évènements on gère
    //on pourra utiliser dans un controleur $this->denyAccessUnlessGranted('edit', $article);
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        // on vérifie que traite bien un Article
        if (!$subject instanceof Article) {
            return false;
        }

        return true;

    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // grâce à la méthode support(), on sait que $subject est un objet Article
        /** @var Article $article */
        $article = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($article, $user);
            case self::EDIT:
                return $this->canEdit($article, $user);
            case self::DELETE:
                return $this->canDelete($article, $user);
        }

        return false;
    }

    private function canView(Article $article, User $user)
    {
        // on part du principe que si on peut modifier, on peut voir
        if ($this->canEdit($article, $user)) {
            return true;
        }

        //pas le droit
        return false;
    }

    private function canEdit(Article $article, User $user)
    {
        // on veut que seuls les auteurs des articles puissent les modifier
        return $user === $article->getUser();
    }

    private function canDelete(Article $article, User $user)
    {
        // on veut que seuls les auteurs des articles puissent les supprimer
        return $user === $article->getUser();
    }

}
