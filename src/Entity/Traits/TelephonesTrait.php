<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait TelephonesTrait
{
    /**
     * @var string|null
     */
    protected $telephones;

    /**
     * @return string|null
     */
    public function getTelephones(): ?string
    {
        return $this->telephones;
    }

    /**
     * @param string $telephones
     */
    public function setTelephones(?string $telephones): void
    {
        $this->telephones = $telephones;
    }

}
