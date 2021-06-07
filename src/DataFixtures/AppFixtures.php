<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');


            $villes = array('Rennes', 'Nantes', 'Quimper', 'Niort');

            foreach ($villes as $item){
                $ville = new Site();
                $ville->setNom($item);
                $manager->persist($ville);
                $manager->flush();
            }



        for ($i = 0; $i <= 10; $i++){
            $lieu = new Lieu();

            $villeManager = $manager->getRepository(Ville::class);
            $villes = $villeManager->findAll();

            $lieu->setNom($generator->words(3, true));
            $lieu->setRue($generator->numberBetween(1,20).', rue de '.$generator->word);
            $lieu->setVille($generator->randomElement($villes));

            $manager->persist($lieu);
            $manager->flush();

        }

        $sites = array('Rennes', 'Nantes', 'Quimper', 'Niort');

        foreach ($sites as $item){
            $site = new Site();
            $site->setNom($item);
            $manager->persist($site);
            $manager->flush();
        }

        for ($i = 0; $i <= 10; $i++){
            $user = new User();

            $siteManager = $manager->getRepository(Site::class);
            $sites = $siteManager->findAll();

            $user->setPrenom($generator->firstName);
            $user->setNom($generator->lastName);
            $user->setPseudo($user->getPrenom().$generator->numberBetween(1,9));
            $password = $this->encoder->encodePassword($user, 'pass_1234');
            $user->setPassword($password);
            $user->setMail($generator->email);
            $user->setTelephone($generator->phoneNumber);
            $user->setActif($generator->boolean);
            $user->setSite($generator->randomElement($sites));
            $user->setRoles($generator->randomElement(array(['ROLE_USER'], ['ROLE_ADMIN'])));

            $manager->persist($user);
            $manager->flush();
        }


        $listeEtats = array('Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Passée', 'Annulée');

        foreach ($listeEtats as $item){
            $etat = new Etat();
            $etat->setLibelle($item);
            $manager->persist($etat);
            $manager->flush();
        }

        for ($i = 0; $i <= 20; $i++){

            $sortie = new Sortie();

            $lieuManager = $manager->getRepository(Lieu::class);
            $lieux = $lieuManager->findAll();

            $siteManager = $manager->getRepository(Site::class);
            $sites = $siteManager->findAll();

            $userManager = $manager->getRepository(User::class);
            $users = $userManager->findAll();

            $etatManager = $manager->getRepository(Etat::class);

            $sortie->setNom($generator->words(3, true));
            $sortie->setDescription($generator->words(20, true));
            $sortie->setDuree($generator->numberBetween(1,24));
            $sortie->setLieu($generator->randomElement($lieux));
            $sortie->setOrganisateur($generator->randomElement($users));
            $sortie->setDateDebut($generator->dateTimeBetween('-1 month', '+ 5 months'));
            $sortie->setDateCloture($generator->dateTimeBetween('-5 months', $sortie->getDateDebut()));

                if(new DateTime('now') < $sortie->getDateCloture()){
                    $sortie->setEtat($etatManager->findOneBy(['libelle' => 'Ouverte']));
                }
                if($sortie->getDateDebut() === new DateTime('now')){
                    $sortie->setEtat($etatManager->findOneBy(['libelle' => 'Activité en cours']));
                }
                if(new DateTime('now') > $sortie->getDateCloture()) {
                    if ($sortie->getDateDebut() === new DateTime('now')) {
                        $sortie->setEtat($etatManager->findOneBy(['libelle' => 'Cloturée']));
                    }
                }
                if(new DateTime('now') > $sortie->getDateDebut()){
                    if ($sortie->getDateDebut() === new DateTime('now')) {
                        $sortie->setEtat($etatManager->findOneBy(['libelle' => 'Passée']));
                    }
                } else {
                    $sortie->setEtat($generator->randomElement([$etatManager->findOneBy(['libelle' => 'Créée']), $etatManager->findOneBy(['libelle' => 'Annulée'])]));
                }

            $sortie->setNbInscriptionsMax($generator->numberBetween(5,25));
            $sortie->setSite($generator->randomElement($sites));

            $manager->persist($sortie);
            $manager->flush();
        }

        for ($i = 0; $i <= 50; $i++){

            $inscription = new Inscription();

            $sortieManager = $manager->getRepository(Sortie::class);
            $sorties = $sortieManager->findAll();

            $userManager = $manager->getRepository(User::class);
            $users = $userManager->findAll();

            $inscription->setParticipant($generator->randomElement($users));
            $inscription->setSortie($generator->randomElement($sorties));
            $inscription->setDateInscription($generator->dateTimeBetween('- 2 month', 'now'));

            $manager->persist($inscription);
            $manager->flush();
        }

    }
}
