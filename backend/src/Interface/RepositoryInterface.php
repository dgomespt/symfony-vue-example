<?php

namespace App\Interface;


use Doctrine\Common\Collections\Collection;

interface RepositoryInterface
{
    public function all(): Collection;

}