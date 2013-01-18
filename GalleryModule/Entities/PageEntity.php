<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace GalleryModule\Entities;

use Venne;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @ORM\Entity(repositoryClass="\DoctrineModule\Repositories\BaseRepository")
 * @ORM\Table(name="gallery_page")
 * @ORM\DiscriminatorEntry(name="galleryPage")
 */
class PageEntity extends \CmsModule\Content\Entities\PageEntity
{

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection|CategoryEntity[]
	 * @ORM\OneToMany(targetEntity="CategoryEntity", mappedBy="page")
	 */
	protected $categories;

	/**
	 * @ORM\OneToOne(targetEntity="\CmsModule\Content\Entities\DirEntity", cascade={"all"})
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	protected $dir;


	public function __construct()
	{
		parent::__construct();

		$this->mainRoute->type = 'Gallery:Page:default';

		$this->dir = new \CmsModule\Content\Entities\DirEntity();
		$this->dir->setInvisible(true);
		$this->dir->setName('galleryPage');
	}


	public function setCategories($categories)
	{
		$this->categories = $categories;
	}


	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|CategoryEntity[]
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	public function getDir()
	{
		return $this->dir;
	}
}
