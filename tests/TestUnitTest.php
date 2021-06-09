<?php

namespace App\Tests;

use App\Controller\VilleController;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TestUnitTest extends TestCase
{
    public function testVilleRemove(VilleController $villeController): void
    {
        $ville1 = $entityManager->
        $ville2 = new Ville();
        $lieu1 = new Lieu();
        $lieu2 = new Lieu();
        $sortie = new Sortie();

        //La ville2 est associée au lieu2 qui est lui même associé à une sortie
        $sortie->setLieu($lieu2);
        $lieu2->setVille($ville2);

        //La ville1 est associée au lieu1 qui n'est associé à aucune sortie
        $lieu1->setVille($ville1);

        //Test de suppression de la ville2



    }

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }
}
