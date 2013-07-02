<?php
namespace Rhapsody\CommonsBundle\Model;

class Group implements GroupInterface
{
	protected $name;
	protected $order;

	public function __construct()
	{

	}

	public function getName()
	{
		return $this->name;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setOrder($order)
	{
		$this->order = $order;
	}
}