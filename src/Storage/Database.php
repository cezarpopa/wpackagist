<?php

namespace Outlandish\Wpackagist\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Outlandish\Wpackagist\Entity\PackageData;

final class Database extends Provider
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(string $key): ?string
    {
        $data = $this->loadFromDb($key);
        if ($data) {
            return $data->getValue();
        }

        return null;
    }

    public function save(string $key, string $json): bool
    {
        // Update or insert as needed.
        $data = $this->loadFromDb($key);
        if (!$data) {
            $data = new PackageData();
        }

        $data->setKey($key);
        $data->setValue($json);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return true;
    }

    protected function loadFromDb(string $key): ?PackageData
    {
        return $this->entityManager->getRepository(PackageData::class)->findOneBy(['key' => $key]);
    }
}
