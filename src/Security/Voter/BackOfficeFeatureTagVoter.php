<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureTag;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BackOfficeFeatureTagVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit'])
            && $subject instanceof FeatureTag;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var FeatureTag $subject */

        $company = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$company instanceof Company) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        $featureTag = $subject;
        switch ($attribute) {
            case 'edit':
                if($featureTag->getCompany() === $company){
                    return true;
                }
                break;
        }

        return false;
    }
}