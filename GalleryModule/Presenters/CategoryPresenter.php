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

use DoctrineModule\Repositories\BaseRepository;
use Venne;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class CategoryPresenter extends \CmsModule\Content\Presenters\PagePresenter
{

	/** @var BaseRepository */
	protected $categoryRepository;


	/**
	 * @param \DoctrineModule\Repositories\BaseRepository $categoryRepository
	 */
	public function __construct(BaseRepository $categoryRepository)
	{
		parent::__construct();

		$this->categoryRepository = $categoryRepository;
	}


	public function renderDefault()
	{
		$this->template->category = $this->categoryRepository->findOneBy(array('route' => $this->route->id));
	}
}
