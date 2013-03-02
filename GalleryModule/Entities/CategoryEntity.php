<?php

namespace GalleryModule\Entities;

use Venne;
use Nette\Utils\Strings;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use CmsModule\Content\Entities\DirEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @ORM\Entity(repositoryClass="\GalleryModule\Repositories\CategoryRepository")
 * @ORM\Table(name="gallery_category")
 * @ORM\HasLifecycleCallbacks
 */
class CategoryEntity extends \DoctrineModule\Entities\IdentifiedEntity
{

	/**
	 * @var PageEntity
	 * @ORM\ManyToOne(targetEntity="PageEntity")
	 * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $page;

	/**
	 * @var \CmsModule\Content\Entities\RouteEntity
	 * @ORM\OneToOne(targetEntity="\CmsModule\Content\Entities\RouteEntity", cascade={"persist"}, orphanRemoval=true)
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	protected $route;

	/**
	 * @var PhotoEntity[]
	 * @ORM\OneToMany(targetEntity="PhotoEntity", mappedBy="category", cascade={"persist"}, orphanRemoval=true)
	 * @ORM\OrderBy({"position" = "ASC"})
	 */
	protected $photos;

	/**
	 * @var DirEntity
	 * @ORM\OneToOne(targetEntity="\CmsModule\Content\Entities\DirEntity", cascade={"all"})
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	protected $dir;

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $description = '';

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $updated;


	/**
	 * @param PageEntity $page
	 * @param $name
	 */
	public function __construct(PageEntity $page, $name)
	{
		$this->page = $page;
		$this->name = $name;

		$this->route = new \CmsModule\Content\Entities\RouteEntity;
		$this->route->setType('Gallery:Category:default');
		$this->route->setLocalUrl(Strings::webalize($name));
		$this->route->setPage($this->page);
		$this->route->setParent($this->page->mainRoute);
		$this->route->setTitle($name);
		$this->page->routes[] = $this->route;

		$this->photos = new ArrayCollection;

		$this->dir = new \CmsModule\Content\Entities\DirEntity();
		$this->dir->setInvisible(true);
		$this->dir->setParent($page->getDir());

		$this->setName($name);

		$this->created = new \Nette\DateTime();
	}


	/**
	 * @ORM\PreUpdate()
	 */
	public function preUpdate()
	{
		$this->updated = new \Nette\DateTime();
	}


	public function __toString()
	{
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
		$this->route->setLocalUrl(Strings::webalize($name));
		$this->route->setTitle($name);
		$this->dir->setName($name);
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * @param \GalleryModule\Entities\PageEntity $page
	 */
	public function setPage($page)
	{
		$this->page = $page;
	}


	/**
	 * @return \GalleryModule\Entities\PageEntity
	 */
	public function getPage()
	{
		return $this->page;
	}


	/**
	 * @param $dir
	 */
	public function setDir($dir)
	{
		$this->dir = $dir;
	}


	/**
	 * @return \CmsModule\Content\Entities\DirEntity
	 */
	public function getDir()
	{
		return $this->dir;
	}


	/**
	 * @param \CmsModule\Content\Entities\RouteEntity $route
	 */
	public function setRoute($route)
	{
		$this->route = $route;
	}


	/**
	 * @return \CmsModule\Content\Entities\RouteEntity
	 */
	public function getRoute()
	{
		return $this->route;
	}


	/**
	 * @param $photos
	 */
	public function setPhotos($photos)
	{
		$this->photos = $photos;
	}


	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|PhotoEntity[]
	 */
	public function getPhotos()
	{
		return $this->photos;
	}


	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}


	/**
	 * @return \DateTime
	 */
	public function getUpdated()
	{
		return $this->updated;
	}
}
