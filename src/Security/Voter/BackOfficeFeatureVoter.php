<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Feature;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BackOfficeFeatureVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit'])
            && $subject instanceof Feature;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Feature $subject */

        $company = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$company instanceof Company) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        $feature = $subject;
        switch ($attribute) {
            case 'edit':
                if($feature->getCompany() === $company){
                    return true;
                }
                break;
        }

        return false;
    }
}
