<?php
class ControllerExtensionPaymentEcontCod extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['continue'] = $this->url->link('checkout/success');

		if (!version_compare(VERSION, '2.2', '>=')) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/econt_cod.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/payment/econt_cod.tpl', $data);
			} else {
				return $this->load->view('default/template/extension/payment/econt_cod.tpl', $data);
			}
		} else {
			return $this->load->view('extension/payment/econt_cod', $data);
		}
	}

	public function confirm() {
		$json = array();
		
		if ($this->session->data['payment_method']['code'] == 'econt_cod') {
			$this->load->model('checkout/order');
			
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_econt_cod_order_status_id'));
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

}
?>
