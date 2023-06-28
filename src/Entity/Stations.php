<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stations
 *
 * @ORM\Table(name="stations")
 * @ORM\Entity
 */
class Stations
{
    /**
     * @var int
     *
     * @ORM\Column(name="station_name", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $stationName;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $longitude;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $latitude;

    /**
     * @return int
     */
    public function getStationName(): int
    {
        return $this->stationName;
    }

    /**
     * @param int $stationName
     */
    public function setStationName(int $stationName): void
    {
        $this->stationName = $stationName;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }


}
