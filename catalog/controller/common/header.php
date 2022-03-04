<?php class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		}
		else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['base']               = $server;
		$data['description']        = $this->document->getDescription();
		$data['direction']          = $this->language->get('direction');
		$data['keywords']           = $this->document->getKeywords();
		$data['lang']               = $this->language->get('code');
		$data['links']              = $this->document->getLinks();
		$data['name']               = $this->config->get('config_name');
		$data['scripts']            = $this->document->getScripts('header');
		$data['styles']             = $this->document->getStyles();
		$data['title']              = $this->document->getTitle();

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		}
		else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		}
		else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged']        = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['account']            = $this->url->link('account/account', '', true);

		$data['cart']               = $this->load->controller('common/cart');
		$data['checkout']           = $this->url->link('checkout/checkout', '', true);
		$data['contact']            = $this->url->link('information/contact');
		$data['currency']           = $this->load->controller('common/currency');

		$data['download']           = $this->url->link('account/download', '', true);
		$data['home']               = $this->url->link('common/home');

		$data['language']           = $this->load->controller('common/language');
		$data['logged']             = $this->customer->isLogged();
		$data['login']              = $this->url->link('account/login', '', true);
		$data['logout']             = $this->url->link('account/logout', '', true);

		$data['menu']               = $this->load->controller('common/menu');

		$data['order']              = $this->url->link('account/order', '', true);
		$data['register']           = $this->url->link('account/register', '', true);

		$data['search']             = $this->load->controller('common/search');
		$data['shopping_cart']      = $this->url->link('checkout/cart');

		$data['telephone']          = $this->config->get('config_telephone');
		$data['transaction']        = $this->url->link('account/transaction', '', true);
		$data['wishlist']           = $this->url->link('account/wishlist', '', true);

		return $this->load->view('common/header', $data);
	}
}
