<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Insight;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BackOfficeInsightVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit'])
            && $subject instanceof Insight;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $company = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$company instanceof Company) {
            return false;
        }

        /** @var Insight $subject */

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'edit':
                if($subject->getFeedback()->getCompany() === $company
                   && $subject->getFeature()->getCompany() === $company)
                {
                    return true;
                }
                break;
        }

        return false;
    }
}
