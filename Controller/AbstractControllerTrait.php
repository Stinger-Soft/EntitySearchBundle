<?php
/**
 * Created by PhpStorm.
 * User: FlorianMeyer
 * Date: 18.01.2018
 * Time: 13:09
 */

namespace StingerSoft\EntitySearchBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

trait AbstractControllerTrait {

	/**
	 * Returns a RedirectResponse to the given route with the given parameters.
	 *
	 * @param string $route      The name of the route
	 * @param array  $parameters An array of parameters
	 * @param int    $status     The status code to use for the Response
	 *
	 * @return RedirectResponse
	 */
	protected abstract function redirectToRoute($route, array $parameters = array(), $status = 302);


	/**
	 * Creates and returns a Form instance from the type of the form.
	 *
	 * @param string $type    The fully qualified class name of the form type
	 * @param mixed  $data    The initial data for the form
	 * @param array  $options Options for the form
	 *
	 * @return FormInterface
	 */
	protected abstract function createForm($type, $data = null, array $options = array());

	/**
	 * Renders a view.
	 *
	 * @param string   $view       The view name
	 * @param array    $parameters An array of parameters to pass to the view
	 * @param Response $response   A response instance
	 *
	 * @return Response A Response instance
	 */
	protected abstract function render($view, array $parameters = array(), Response $response = null);

}