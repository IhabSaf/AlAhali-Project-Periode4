<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Measurement
 *
 * @ORM\Table(name="measurement")
 * @ORM\Entity
 */
class Measurement
{
    /**
     * @var int
     *
     * @ORM\Column(name="measurement_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $measurementId;

    /**
     * @var string
     *
     * @ORM\Column(name="station_name", type="string", length=10, nullable=false)
     */
    private $stationName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

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
     * @var float
     *
     * @ORM\Column(name="stp", type="float", precision=10, scale=0, nullable=false)
     */
    private $stp;

    /**
     * @var float
     *
     * @ORM\Column(name="cldc", type="float", precision=10, scale=0, nullable=false)
     */
    private $cldc;

    /**
     * @param int $measurementId
     */
    public function setMeasurementId(int $measurementId): void
    {
        $this->measurementId = $measurementId;
    }

    /**
     * @param string $stationName
     */
    public function setStationName(string $stationName): void
    {
        $this->stationName = $stationName;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @param float $stp
     */
    public function setStp(float $stp): void
    {
        $this->stp = $stp;
    }

    /**
     * @param float $cldc
     */
    public function setCldc(float $cldc): void
    {
        $this->cldc = $cldc;
    }




}
