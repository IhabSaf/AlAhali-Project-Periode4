<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Measurement
 *
 * @ORM\Table(name="measurement", indexes={@ORM\Index(name="FK_measurement_station_idx", columns={"station_name"})})
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
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

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
     * @var Stations
     *
     * @ORM\ManyToOne(targetEntity="Stations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station_name", referencedColumnName="station_name")
     * })
     */
    private $stationName;

    /**
     * @return int
     */
    public function getMeasurementId(): int
    {
        return $this->measurementId;
    }

    /**
     * @param int $measurementId
     */
    public function setMeasurementId(int $measurementId): void
    {
        $this->measurementId = $measurementId;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return float
     */
    public function getStp(): float
    {
        return $this->stp;
    }

    /**
     * @param float $stp
     */
    public function setStp(float $stp): void
    {
        $this->stp = $stp;
    }

    /**
     * @return float
     */
    public function getCldc(): float
    {
        return $this->cldc;
    }

    /**
     * @param float $cldc
     */
    public function setCldc(float $cldc): void
    {
        $this->cldc = $cldc;
    }

    /**
     * @return Stations
     */
    public function getStationName(): Stations
    {
        return $this->stationName;
    }

    /**
     * @param Stations $stationName
     */
    public function setStationName(Stations $stationName): void
    {
        $this->stationName = $stationName;
    }


}
