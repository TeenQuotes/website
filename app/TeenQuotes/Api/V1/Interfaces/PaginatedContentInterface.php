<?php

namespace TeenQuotes\Api\V1\Interfaces;

interface PaginatedContentInterface
{
    public function getPagesize();
    public function getPage();
}
