<?php namespace CodeIgniter\Debug\Toolbar\Collectors;

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package      CodeIgniter
 * @author       CodeIgniter Dev Team
 * @copyright    Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license      https://opensource.org/licenses/MIT	MIT License
 * @link         https://codeigniter.com
 * @since        Version 4.0.0
 * @filesource
 */

use CodeIgniter\Services;

/**
 * Routes collector
 */
class Routes extends BaseCollector
{
	/**
	 * Whether this collector has data that can
	 * be displayed in the Timeline.
	 *
	 * @var bool
	 */
	protected $hasTimeline = false;

	/**
	 * Whether this collector needs to display
	 * content in a tab or not.
	 *
	 * @var bool
	 */
	protected $hasTabContent = true;

	/**
	 * The 'title' of this Collector.
	 * Used to name things in the toolbar HTML.
	 *
	 * @var string
	 */
	protected $title = 'Routes';

	//--------------------------------------------------------------------

	/**
	 * Builds and returns the HTML needed to fill a tab to display
	 * within the Debug Bar
	 *
	 * @return string
	 */
	public function display(): string
	{
		$parser = \Config\Services::parser();

		$rawRoutes = Services::routes(true);
		$router = Services::router(null, true);

		/*
		 * Matched Route
		 */
		$route = $router->getMatchedRoute();

		// Get our parameters
		$method = is_callable($router->controllerName()) ? new \ReflectionFunction($router->controllerName()) : new \ReflectionMethod($router->controllerName(), $router->methodName());
		$rawParams = $method->getParameters();

		$params = [];
		foreach ($rawParams as $key => $param)
		{
			$params[] = [
				'name'  => $param->getName(),
				'value' => $router->params()[$key] ?:
					"&lt;empty&gt;&nbsp| default: ". var_export($param->getDefaultValue(), true)
			];
		}

		$matchedRoute = [
			[
				'directory'  => $router->directory(),
			'controller' => $router->controllerName(),
			'method'     => $router->methodName(),
			'paramCount' => count($router->params()),
			'truePCount' => count($params),
			'params'     => $params ?? []
			]
		];

		/*
		 * Defined Routes
		 */
		$rawRoutes = $rawRoutes->getRoutes();
		$routes    = [];

		foreach ($rawRoutes as $from => $to)
		{
			$routes[] = [
				'from' => $from,
				'to'   => $to
			];
		}

		return $parser->setData([
				'matchedRoute' => $matchedRoute,
				'routes' => $routes
		])
			->render('CodeIgniter\Debug\Toolbar\Views\_routes.tpl');
	}

	//--------------------------------------------------------------------
}
