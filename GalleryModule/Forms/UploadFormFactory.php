<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace GalleryModule\Forms;

use GalleryModule\Entities\CategoryEntity;
use GalleryModule\Entities\PhotoEntity;
use Venne;
use Venne\Forms\Form;
use DoctrineModule\Forms\FormFactory;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class UploadFormFactory extends FormFactory
{


	protected function getControlExtensions()
	{
		return array_merge(parent::getControlExtensions(), array(
			new \CmsModule\Content\Forms\ControlExtensions\ControlExtension(),
			new \FormsModule\ControlExtensions\ControlExtension(),
		));
	}


	public function configure(Form $form)
	{
		$form->addFileEntityInput('_photos', 'Upload photos')
			->setMulti(TRUE);

		$form->addSaveButton('Upload');
	}


	public function handleSave(Form $form)
	{
		/** @var $entity CategoryEntity */
		$entity = $form->data;
		$data = $form->getValues();

		foreach ($data['_photos'] as $fileEntity) {
			if ($fileEntity) {
				$entity->photos[] = $photoEntity = new PhotoEntity($entity, '');
				$photoEntity->setPhoto($fileEntity);
			}
		}

		parent::handleSave($form);
	}
}
