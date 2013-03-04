<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace GalleryModule\Components;

use GalleryModule\Entities\CategoryEntity;
use GalleryModule\Entities\PhotoEntity;
use GalleryModule\Forms\GalleryFormFactory;
use GalleryModule\Forms\PhotoFormFactory;
use GalleryModule\Forms\SortFormFactory;
use GalleryModule\Forms\UploadFormFactory;
use GalleryModule\Repositories\CategoryRepository;
use GalleryModule\Repositories\PhotoRepository;
use Kdyby\Extension\Forms\BootstrapRenderer\BootstrapRenderer;
use CmsModule\Content\SectionControl;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class GalleriesControl extends SectionControl
{

	/** @persistent */
	public $key;

	/** @persistent */
	public $id;

	/** @var CategoryRepository */
	protected $categoryRepository;

	/** @var PhotoRepository */
	protected $photoRepository;

	/** @var ProductFormFactory */
	protected $galleryFormFactory;

	/** @var UploadFormFactory */
	protected $uploadFormFactory;

	/** @var SortFormFactory */
	protected $sortFormFactory;

	/** @var PhotoFormFactory */
	protected $photoFormFactory;


	public function __construct(CategoryRepository $categoryRepository, PhotoRepository $photoRepository, GalleryFormFactory $galleryFormFactory, UploadFormFactory $uploadFormFactory, SortFormFactory $sortFormFactory, PhotoFormFactory $photoFormFactory)
	{
		parent::__construct();

		$this->categoryRepository = $categoryRepository;
		$this->photoRepository = $photoRepository;
		$this->galleryFormFactory = $galleryFormFactory;
		$this->uploadFormFactory = $uploadFormFactory;
		$this->sortFormFactory = $sortFormFactory;
		$this->photoFormFactory = $photoFormFactory;
	}


	/**
	 * @return CategoryEntity
	 */
	public function getCategoryEntity()
	{
		return $this->key ? $this->categoryRepository->find($this->key) : NULL;
	}


	/**
	 * @return PhotoEntity
	 */
	public function getPhotoEntity()
	{
		return $this->id ? $this->photoRepository->find($this->id) : NULL;
	}


	public function handleDelete($id)
	{
		$categoryEntity = $this->getCategoryEntity();

		foreach ($categoryEntity->getPhotos() as $key => $photo) {
			if ($photo->id == $id) {
				$categoryEntity->getPhotos()->remove($key);
				break;
			}
		}

		$this->categoryRepository->save($categoryEntity);

		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}

		$this->presenter->payload->url = $this->link('this');
		$this->invalidateControl('view');
		$this->id = NULL;
	}


	public function handleEdit($id)
	{
		$this->id = $id;

		if (!$this->presenter->isAjax()) {
			$this->redirect('this', array('id' => $id));
		}

		$this->presenter->payload->url = $this->link('this', array('id' => $id));
		$this->invalidateControl('form');
	}


	protected function createComponentTable()
	{
		$_this = $this;

		$table = new \CmsModule\Components\Table\TableControl;
		$table->setTemplateConfigurator($this->templateConfigurator);
		$table->setRepository($this->categoryRepository);

		// forms
		$form = $table->addForm($this->galleryFormFactory, 'Gallery', function () use ($_this) {
			return new \GalleryModule\Entities\CategoryEntity($_this->entity, '');
		});

		// navbar
		$table->addButtonCreate('create', 'Create new', $form, 'shopping-cart');

		$table->addColumn('name', 'Name')
			->setWidth('40%')
			->setSortable(TRUE)
			->setFilter();
		$table->addColumn('description', 'Description')
			->setWidth('60%')
			->setSortable(TRUE)
			->setFilter();

		$repository = $this->categoryRepository;
		$presenter = $this;
		$action = $table->addAction('on', 'On');
		$action->onClick[] = function ($button, $entity) use ($presenter, $repository) {
			$entity->route->published = TRUE;
			$repository->save($entity);

			if (!$presenter->presenter->isAjax()) {
				$presenter->redirect('this');
			}

			$presenter['table']->invalidateControl('table');
			$presenter->presenter->payload->url = $presenter->link('this');
		};
		$action->onRender[] = function ($button, $entity) use ($presenter, $repository) {
			$button->setDisabled($entity->route->published);
		};

		$action = $table->addAction('off', 'Off');
		$action->onClick[] = function ($button, $entity) use ($presenter, $repository) {
			$entity->route->published = FALSE;
			$repository->save($entity);

			if (!$presenter->presenter->isAjax()) {
				$presenter->redirect('this');
			}

			$presenter['table']->invalidateControl('table');
			$presenter->presenter->payload->url = $presenter->link('this');
		};
		$action->onRender[] = function ($button, $entity) use ($presenter, $repository) {
			$button->setDisabled(!$entity->route->published);
		};

		$table->addAction('show', 'Edit')->onClick[] = function ($button, $entity) use ($_this) {
			if (!$_this->presenter->isAjax()) {
				$_this->redirect('this', array('key' => $entity->id));
			}
			$_this->presenter->payload->url = $_this->link('this', array('key' => $entity->id));
			$_this->key = $entity->id;
		};
		$table->addActionDelete('delete', 'Delete');

		// global actions
		$table->setGlobalAction($table['delete']);

		return $table;
	}


	protected function createComponentGalleryForm()
	{
		$form = $this->galleryFormFactory->invoke($this->getCategoryEntity());
		$form->onSuccess[] = $this->galleryFormSuccess;
		return $form;
	}


	protected function createComponentUploadForm()
	{
		$form = $this->uploadFormFactory->invoke($this->getCategoryEntity());
		$form->onSuccess[] = $this->uploadFormSuccess;
		return $form;
	}


	protected function createComponentSortForm()
	{
		$form = $this->sortFormFactory->invoke($this->getCategoryEntity());
		$form->onSuccess[] = $this->sortFormSuccess;
		return $form;
	}


	protected function createComponentPhotoForm()
	{
		$form = $this->photoFormFactory->invoke($this->getPhotoEntity());
		$form->onSuccess[] = $this->photoFormSuccess;
		$form->getSaveButton()->getControlPrototype()->onClick = '_a = window.alert; window.alert = function() {}; if(Nette.validateForm(this)) { $(this).parents(".modal").each(function(){ $(this).modal("hide"); }); } window.alert = _a';
		return $form;
	}


	public function uploadFormSuccess($form)
	{
		$this->redirect('this');
	}


	public function galleryFormSuccess($form)
	{
		$this->flashMessage('Gallery has been saved.', 'success');

		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}

		$this->invalidateControl('view');
	}


	public function sortFormSuccess($form)
	{
		$this->flashMessage('Layout has been stored.', 'success');

		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}

		$this->invalidateControl('view');
	}


	public function photoFormSuccess($form)
	{
		$this->presenter->flashMessage('Photo has been updated.', 'success');

		if (!$this->presenter->isAjax()) {
			$this->redirect('this', array('id' => NULL));
		}

		$this->presenter->payload->url = $this->link('this', array('id' => NULL));
		$this->invalidateControl('form');
		$this->id = NULL;
	}


	public function render()
	{
		$this->template->categoryRepository = $this->categoryRepository;
		$this->template->render();
	}
}
