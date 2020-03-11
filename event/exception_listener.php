<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\event;

use phpbbstudio\ass\exceptions\shop_inactive_exception;
use phpbbstudio\ass\exceptions\shop_item_exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use phpbbstudio\ass\exceptions\shop_exception;

/**
 * phpBB Studio - Advanced Shop System: Exception listener
 */
class exception_listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbbstudio\ass\helper\controller */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\type_cast_helper */
	protected $type_caster;

	/** @var bool */
	protected $debug;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\db_text				$config_text		Config text object
	 * @param  \phpbbstudio\ass\helper\controller	$controller			ASS Controller helper object
	 * @param  \phpbb\language\language				$language			Language object
	 * @param  \phpbb\textformatter\s9e\renderer	$renderer			Text formatter renderer object
	 * @param  \phpbb\template\template				$template			Template object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\db_text $config_text,
		\phpbbstudio\ass\helper\controller $controller,
		\phpbb\language\language $language,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbb\template\template $template
	)
	{
		$this->config_text	= $config_text;
		$this->controller	= $controller;
		$this->language		= $language;
		$this->renderer		= $renderer;
		$this->template		= $template;

		$this->type_caster	= new \phpbb\request\type_cast_helper();
		$this->debug		= defined('DEBUG');
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 * @access public
	 * @static
	 */
	static public function getSubscribedEvents()
	{
		return [
			KernelEvents::EXCEPTION		=> 'on_kernel_exception',
		];
	}

	/**
	 * Handle any shop exception.
	 *
	 * @param  GetResponseForExceptionEvent		$event		The event object
	 * @return void
	 * @access public
	 */
	public function on_kernel_exception(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();

		if ($exception instanceof shop_exception)
		{
			$message = $exception->getMessage();
			$this->type_caster->set_var($message, $message, 'string', true, false);

			$message = $this->language->lang_array($message, $exception->get_parameters());

			// Show <strong> text in bold
			$message = preg_replace('#&lt;(/?strong)&gt;#i', '<$1>', $message);

			if (!$event->getRequest()->isXmlHttpRequest())
			{
				$this->controller->create_shop('shop');

				page_header($this->language->lang('INFORMATION'));

				if ($exception instanceof shop_inactive_exception)
				{
					$desc = $this->config_text->get('ass_inactive_desc');
					$desc = $this->renderer->render(htmlspecialchars_decode($desc, ENT_COMPAT));
				}

				if ($exception instanceof  shop_item_exception)
				{
					$desc = $this->language->lang('ASS_ERROR_LOGGED');
				}

				$this->template->assign_vars([
					'EXCEPTION_CODE'	=> $exception->getStatusCode(),
					'EXCEPTION_TEXT'	=> $message,
					'EXCEPTION_DESC'	=> !empty($desc) ? $desc : '',
				]);

				$this->template->set_filenames([
					'body' => 'ass_exception.html',
				]);

				page_footer(true, false, false);

				$response = new Response($this->template->assign_display('body'), 500);
			}
			else
			{
				$data = [
					'title'	=> $this->language->lang('INFORMATION'),
				];

				if (!empty($message))
				{
					$data['message'] = $message;
				}

				if ($this->debug)
				{
					$data['trace'] = $exception->getTrace();
				}

				$response = new JsonResponse($data, 500);
			}

			$response->setStatusCode($exception->getStatusCode());
			$response->headers->add($exception->getHeaders());

			$event->setResponse($response);
		}
	}
}
