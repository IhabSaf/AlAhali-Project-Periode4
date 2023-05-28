<?php



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


}
