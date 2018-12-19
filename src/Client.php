<?php
namespace Yumdap\Magento2;

use Zttp\Zttp;

class Client
{
    /**
     * path to the Magento 2 REST API
     * e.g. https://example.com/rest
     * @var string
     */
    protected $api_url;

    /**
     * Bearer token to access the Magento 2 API.
     * see http://devdocs.magento.com/guides/v2.1/get-started/authentication/gs-authentication-token.html
     * @var string
     */
    protected $accessToken;

    /** @var \Zttp\Zttp */
    protected $client;

    public function __construct($api_url, $accessToken)
    {
        $this->api_url = $api_url;
        $this->accessToken = $accessToken;
        $this->client = Zttp::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
        ]);
    }

    /**
     * get a specific order by increment_id
     * @param  string $incrementId the order id
     * @return array               the order array
     */
    public function getOrderByIncrementId($incrementId)
    {
        $parameters = [
            'searchCriteria[filter_groups][0][filters][0][field]' => 'increment_id',
            'searchCriteria[filter_groups][0][filters][0][value]' => $incrementId,
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq',
        ];

        $response = $this->client->get($this->url('V1/orders'), $parameters)->json();

        return count($response['items']) ? $response['items'][0] : [];
    }

    /**
     * get a specific order by entity_id
     * @param  int    $id the magento 2 entity_id of the order
     * @return array      the order array
     */
    public function getOrderById($id)
    {
        return $this->client->get($this->url("V1/orders/{$id}"))->json();
    }

    public function invoiceOrder($order, $notify = true, $capture = true)
    {
        $order_id = $order['entity_id'];
        $payload = [
            'capture' => $capture,
            'notify' => $notify,
        ];

        $response = $this->client->post($this->url("V1/order/{$order_id}/invoice"), $payload);

        return $response->json();
    }

    /**
     * Create shipment for given order
     * @param  array   $order
     * @param  boolean $notify        send notification to customer
     * @param  boolean $appendComment include comments in notification
     * @return array                  response
     */
    public function shipOrder($order, $notify = true, $appendComment = false)
    {
        $order_id = $order['entity_id'];
        $payload = [
            'items' => $this->getShipableOrderItems($order),
            'notify' => $notify,
            'appendComment' => $appendComment,
        ];

        $response = $this->client->post($this->url("V1/order/{$order_id}/ship"), $payload);

        return $response->json();
    }

    /**
     * Get all shipable items off an order array
     * @param  array  $order
     * @return array         the order items
     */
    protected function getShipableOrderItems($order)
    {
        $items = [];
        foreach ($order['items'] as $item) {
            if (isset($item['parent_item'])) {
                continue;
            }
            $items[] = [
                'order_item_id' => $item['item_id'],
                'qty' => ($item['qty_ordered'] - $item['qty_shipped']),
            ];
        }

        return $items;
    }

    /**
     * get full REST API URL with endpoint
     * @param  string $endpoint the REST endpoint
     * @return string           the full endpoint URL
     */
    protected function url($endpoint)
    {
        return vsprintf('%s/%s', [
            rtrim($this->api_url),
            ltrim($endpoint)
        ]);
    }
}
