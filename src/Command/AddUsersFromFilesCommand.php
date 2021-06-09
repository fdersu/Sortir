<?php

namespace App\Command;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddUsersFromFilesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private string $dataDirectory;

    private SymfonyStyle $io;

    private UserRepository $userRepository;
    private $encoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder

    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;

    }


    public static $defaultName = 'app:add-users-from-files';

    public function configure()
    {
        $this->setDescription('Importer des données en provenance d\'un fichier CSV, XML ou YAML');
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->addUsers();

        return Command::SUCCESS;
    }

    public function getDataFromFiles(): array
    {
        $file = $this->dataDirectory . 'addUser.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        /** @var string $fileString */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);

        if (array_key_exists('results', $data)) {
            return $data['results'];
        }
        return $data;
    }

    public function addUsers(): array
    {
        $users = [];
        $i = 0;
        foreach ($this->getDataFromFiles() as $row) {
            if (array_key_exists('pseudo', $row) && !empty($row['pseudo'])) {
                $user = $this->userRepository->findOneBy([
                    'pseudo' => $row['pseudo']
                ]);
                if (!$user) {
                    $site = $this->entityManager->getRepository(Site::class)->findOneBy(['nom' => $row['site']]);

                    $newUser = new User();
                    $newUser->setPseudo($row['pseudo'])
                        ->setPassword($this->encoder->encodePassword($newUser, $row['password']))
                        ->setNom($row['nom'])
                        ->setRoles(['ROLE_USER'])
                        ->setPrenom($row['prenom'])
                        ->setTelephone($row['telephone'])
                        ->setMail($row['mail'])
                        ->setPhoto($row['photo'])
                        ->setSite($site)
                        ->setActif($row['actif']);

                    $users [$i] = $newUser;
                    $i++;
                }
            }

        }return $users;
    }
}
