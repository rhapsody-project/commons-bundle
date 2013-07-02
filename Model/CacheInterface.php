<?php
namespace Rhapsody\CommonsBundle\Model;

interface CacheInterface
{

	function getCreated();

	function getObject();

	function getTimestamp();
}