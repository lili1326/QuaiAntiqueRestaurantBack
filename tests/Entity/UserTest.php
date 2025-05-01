<?php
namespace App\Tests\Entity;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
class UserTest extends TestCase
{

    public function testTheAutomaticApiTokenSettingWhenAnUserIsCreated(): void
    {
        $user = new User();
        //vérifie que la méthode $user->getApiToken() ne renvoie pas null.
        $this->assertNotNull($user->getApiToken());
    }

    public function testThanAnUserHasAtLeastOneRoleUser():void
    {
        $user = new User();
        //$this->assertContains
        // Vérifie que le tableau retourné par getRoles() contient la chaîne de caractères 'ROLE_USER'.
        $this->assertContains('ROLE_USER',$user->getRoles());
    }

     

}