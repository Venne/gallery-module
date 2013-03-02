<?php

namespace GalleryModule\Entities;

use Venne;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use CmsModule\Content\Entities\FileEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @ORM\Entity(repositoryClass="\GalleryModule\Repositories\PhotoRepository")
 * @ORM\Table(name="gallery_photo")
 */
class PhotoEntity extends \DoctrineModule\Entities\IdentifiedEntity
{

	/**
	 * @var PageEntity
	 * @ORM\ManyToOne(targetEntity="CategoryEntity")
	 * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $category;

	/**
	 * @var \CmsModule\Content\Entities\FileEntity
	 * @ORM\OneToOne(targetEntity="\CmsModule\Content\Entities\FileEntity", cascade={"all"}, orphanRemoval=true)
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	protected $photo;

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string")
	 */
	protected $name = '';

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $description = '';

	/**
	 * @var int
	 * @Gedmo\Translatable
	 * @ORM\Column(type="integer")
	 */
	protected $position;


	/**
	 * @param PageEntity $page
	 * @param $name
	 */
	public function __construct(CategoryEntity $category, $name)
	{
		$this->category = $category;
		$this->name = $name;
		$this->position = time();
	}


	public function __toString()
	{
		return $this->name;
	}


	/**
	 * @param \GalleryModule\Entities\PageEntity $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}


	/**
	 * @return \GalleryModule\Entities\PageEntity
	 */
	public function getCategory()
	{
		return $this->category;
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
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param int $position
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}


	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}


	/**
	 * @param \CmsModule\Content\Entities\FileEntity $photo
	 */
	public function setPhoto(FileEntity $photo = NULL)
	{
		$this->photo = $photo;

		if ($this->photo) {
			$this->photo->setParent($this->category->getDir());
			$this->photo->setInvisible(true);
		}
	}


	/**
	 * @return \CmsModule\Content\Entities\FileEntity
	 */
	public function getPhoto()
	{
		return $this->photo;
	}
}
