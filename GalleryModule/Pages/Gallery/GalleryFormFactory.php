<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace GalleryModule\Pages\Gallery;

use Venne\Forms\Form;
use DoctrineModule\Forms\FormFactory;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class GalleryFormFactory extends FormFactory
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
		$group = $form->addGroup();
		$form->addText('name', 'Name');
		$form
			->addOne('route')
			->setCurrentGroup($group)
			->addTextArea('notation', 'Notation', NULL, 4)->getControlPrototype()->attrs['class'] = 'input-block-level';

		$form->addSaveButton('Save');
	}
}
