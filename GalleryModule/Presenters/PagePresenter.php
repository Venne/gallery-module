<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace GalleryModule\Presenters;

use GalleryModule\Repositories\CategoryRepository;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class PagePresenter extends \CmsModule\Content\Presenters\PagePresenter
{

	/** @var CategoryRepository */
	protected $categoryRepository;


	/**
	 * @param \GalleryModule\Repositories\CategoryRepository $categoryRepository
	 */
	public function injectCategoryRepository(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
	}


	public function getItemsBuilder()
	{
		return $this->getQueryBuilder()
			->setMaxResults($this->page->itemsPerPage)
			->setFirstResult($this['vp']->getPaginator()->getOffset());
	}


	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	protected function getQueryBuilder()
	{
		return $this->categoryRepository->createQueryBuilder("a")
			->leftJoin('a.route', 'r')
			->andWhere('r.published = :true')->setParameter('true', TRUE)
			->andWhere('a.page = :page')->setParameter('page', $this->page->id);
	}


	protected function createComponentVp()
	{
		$vp = new \CmsModule\Components\VisualPaginator;
		$pg = $vp->getPaginator();
		$pg->setItemsPerPage($this->page->itemsPerPage);
		$pg->setItemCount($this->getQueryBuilder()->select("COUNT(a.id)")->getQuery()->getSingleScalarResult());
		return $vp;
	}
}
