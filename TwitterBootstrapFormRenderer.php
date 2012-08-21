<?php

namespace Twitter;


/**
 * Twitter Bootstrap Form Renderer
 */
class FormRenderer extends \Nette\Forms\Rendering\DefaultFormRenderer
{

	public function __construct()
	{
		$this->wrappers['controls']['container'] = 'fieldset';
		$this->wrappers['pair']['container'] = 'div';
		$this->wrappers['label']['container'] = NULL;
		$this->wrappers['control']['container'] = 'div';
	}



	public function init()
	{
		parent::init();

		$this->form->getElementPrototype()->addAttributes(['class' => 'form-horizontal']);
	}



	/**
	 * Renders single visual row.
	 * @param  Nette\Forms\IControl
	 * @return string
	 */
	public function renderPair(\Nette\Forms\IControl $control)
	{
		$pair = $this->getWrapper('pair container');
		$pair->add($this->renderLabel($control));
		$pair->add($this->renderControl($control));
		$pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), TRUE);
		$pair->class($control->getOption('class'), TRUE);

		$pair->class('control-group', TRUE);

		if (++$this->counter % 2) {
			$pair->class($this->getValue('pair .odd'), TRUE);
		}
		$pair->id = $control->getOption('id');
		return $pair->render(0);
	}



	/**
	 * Renders single visual row of multiple controls.
	 * @param  IFormControl[]
	 * @return string
	 */
	public function renderPairMulti(array $controls)
	{
		$s = array();
		foreach ($controls as $control) {
			if (!$control instanceof \Nette\Forms\IControl) {
				throw new \Nette\InvalidArgumentException("Argument must be array of IFormControl instances.");
			}

			$s[] = (string) $control->getControl()->class('btn', TRUE);
		}
		$pair = $this->getWrapper('pair container');

		$pair->class('form-actions', TRUE);

		$pair->add($this->renderLabel($control));
		$pair->add($this->getWrapper('control container')->setHtml(implode(" ", $s)));
		return $pair->render(0);
	}



	/**
	 * Renders 'label' part of visual row of controls.
	 * @param  Nette\Forms\IControl
	 * @return string
	 */
	public function renderLabel(\Nette\Forms\IControl $control)
	{
		$head = $this->getWrapper('label container');

		if ($control instanceof \Nette\Forms\Controls\Checkbox || $control instanceof \Nette\Forms\Controls\Button) {
			return $head->setHtml(($head->getName() === 'td' || $head->getName() === 'th') ? '&nbsp;' : '');

		} else {
			$label = $control->getLabel();
			$label->class('control-label', TRUE);
			$suffix = $this->getValue('label suffix') . ($control->isRequired() ? $this->getValue('label requiredsuffix') : '');
			if ($label instanceof Html) {
				$label->setHtml($label->getHtml() . $suffix);
				$suffix = '';
			}
			return $head->setHtml((string) $label . $suffix);
		}
	}



	/**
	 * Renders 'control' part of visual row of controls.
	 * @param  Nette\Forms\IControl
	 * @return string
	 */
	public function renderControl(\Nette\Forms\IControl $control)
	{
		$body = $this->getWrapper('control container');
		if ($this->counter % 2) {
			$body->class($this->getValue('control .odd'), TRUE);
		}

		$description = $control->getOption('description');
		if ($description instanceof Html) {
			$description = ' ' . $control->getOption('description');

		} elseif (is_string($description)) {
			$description = ' ' . $this->getWrapper('control description')->setText($control->translate($description));

		} else {
			$description = '';
		}

		if ($control->isRequired()) {
			$description = $this->getValue('control requiredsuffix') . $description;
		}

		if ($this->getValue('control errors')) {
			$description .= $this->renderErrors($control);
		}

		$body->class('controls', TRUE);

		if ($control instanceof \Nette\Forms\Controls\Checkbox || $control instanceof \Nette\Forms\Controls\Button) {
			return $body->setHtml((string) $control->getControl() . (string) $control->getLabel() . $description);

		} else {
			return $body->setHtml((string) $control->getControl() . $description);
		}
	}

}
