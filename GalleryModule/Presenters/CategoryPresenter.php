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
class CategoryPresenter extends \CmsModule\Content\Presenters\PagePresenter
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


	public function renderDefault()
	{
		$this->template->category = $this->categoryRepository->findOneBy(array('route' => $this->route->id));
	}
}
