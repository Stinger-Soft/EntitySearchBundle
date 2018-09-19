<?php

/*
 * This file is part of the Stinger Entity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StingerSoft\EntitySearchBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

trait AbstractControllerTrait {

	/**
	 * Returns a RedirectResponse to the given route with the given parameters.
	 *
	 * @param string $route The name of the route
	 * @param array $parameters An array of parameters
	 * @param int $status The status code to use for the Response
	 *
	 * @return RedirectResponse
	 */
	protected abstract function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse;

	/**
	 * Creates and returns a Form instance from the type of the form.
	 *
	 * @param string $type The fully qualified class name of the form type
	 * @param mixed $data The initial data for the form
	 * @param array $options Options for the form
	 *
	 * @return FormInterface
	 */
	protected abstract function createForm(string $type, $data = null, array $options = array()): FormInterface;

	/**
	 * Renders a view.
	 *
	 * @param string $view The view name
	 * @param array $parameters An array of parameters to pass to the view
	 * @param Response $response A response instance
	 *
	 * @return Response A Response instance
	 */
	protected abstract function render($view, array $parameters = array(), ?Response $response = null): Response;

}